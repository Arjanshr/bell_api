<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Notification;
use App\Models\Wishlist;
use App\Models\Campaign;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\NotificationController;

class AuthController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'nullable|string|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
            'gender' => 'nullable|in:male,female',
            'dob' => 'nullable|date',
            'photo' => ['nullable', 'mimes:jpeg,png,jpg,gif,svg,ico,pdf', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 400);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        if ($request->hasFile('photo')) {
            $image_name = rand(0, 99999) . time() . '.' . $request->photo->extension();
            $request->photo->move(storage_path('app/public/profile-photos/'), $image_name);
            $input['profile_photo_path'] = 'profile-photos/' . $image_name;
        }

        $user = User::create($input)->assignRole('customer');

        // 🔔 Send email verification
        event(new Registered($user));

        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User registered successfully. Please verify your email.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $selected_user = User::where('email', $request->email)->first();
            if (!$selected_user)
                return $this->sendError('Unauthorized.', ['error' => 'Credentials do not match...']);

            if ($selected_user->facebook_id == null && $selected_user->google_id == null && $selected_user->github_id == null) {
                if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                    $user = Auth::user();

                    if (is_null($user->email_verified_at)) {
                        // Generate short-lived token just for resending verification
                        $temp_token = $user->createToken('EmailVerify')->plainTextToken;

                        return response()->json([
                            'message' => 'Email not verified.',
                            'error' => 'Please verify your email before logging in.',
                            'resend_verification' => [
                                'url' => url('/api/v1/email/resend'),
                                'token' => $temp_token,
                            ],
                        ], 403);
                    }

                    // Call NotificationController's method instead
                    app(NotificationController::class)->checkWishlistForCampaigns($user->id);
                    $success['token'] = $user->createToken('MyApp')->plainTextToken;
                    $success['name'] = $user->name;

                    return $this->sendResponse($success, 'User login successfully.');
                } else {
                    return $this->sendError('Unauthorized.', ['error' => 'Credentials do not match...']);
                }
            }
        } else {
            $selected_user = User::where('phone', $request->email)->first();
            if (!$selected_user)
                return $this->sendError('Unauthorized.', ['error' => 'Credentials do not match...']);

            if ($selected_user->facebook_id == null && $selected_user->google_id == null && $selected_user->github_id == null) {
                if (Auth::attempt(['phone' => $request->email, 'password' => $request->password])) {
                    $user = Auth::user();
                    app(NotificationController::class)->checkWishlistForCampaigns($user->id);
                    $success['token'] = $user->createToken('MyApp')->plainTextToken;
                    $success['name'] = $user->name;

                    return $this->sendResponse($success, 'User login successfully.');
                } else {
                    return $this->sendError('Unauthorized.', ['error' => 'Credentials do not match...']);
                }
            }
        }

        return $this->sendError('This email has been registered via social login. Please login using your social login links...', ['error' => 'Unauthorized']);
    }

    public function logout()
    {
        $user = auth()->user()->tokens();
        $user->delete();
        return $this->sendResponse(null, 'User logout successfully.');
    }

    public function resetPassword(Request $request)
    {
        // return $request;
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|string|min:6'
        ]);
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(
                    [
                        'password' => bcrypt($password)
                    ]
                )->save();
            }
        );
        return $status === Password::PASSWORD_RESET ? $this->sendResponse(null, __($status)) : $this->sendError(__($status));
    }

    public function resetPasswordSendEmail(Request $request)
    {
        $request->validate([
            'reset_url' => 'required|url',
            'email' => 'required|email'
        ]);
        $status = Password::sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT ? $this->sendResponse(null, __($status)) : $this->sendError(__($status));
    }

    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->sendResponse(null, 'Email already verified.');
        }

        try {
            $request->user()->sendEmailVerificationNotification();
            return $this->sendResponse(null, 'Verification email resent successfully.');
        } catch (TransportExceptionInterface $e) {
            Log::error('Verification email failed: ' . $e->getMessage());

            if (str_contains($e->getMessage(), '550 No Such User Here')) {
                return $this->sendError('Invalid or non-existent email address.', 422);
            }

            return $this->sendError('Failed to resend verification email.', 500);
        } catch (\Exception $e) {
            Log::error('Unexpected email error: ' . $e->getMessage());
            return $this->sendError('An unexpected error occurred.', 500);
        }
    }
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link.'], 403);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) {
                event(new Verified($user));
            }
        }

        // Generate a new token so the frontend can log the user in
        $token = $user->createToken('EmailVerifyLogin')->plainTextToken;

        // Redirect to frontend with token (change this to your actual frontend domain)
        return redirect()->away("https://mobilemandu.com/login?token={$token}&name={$user->name}");
    }
}
