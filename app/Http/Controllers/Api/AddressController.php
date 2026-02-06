<?php


namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\AddressRequest;
use App\Models\Address;
use App\Http\Resources\AddressResource;
use Illuminate\Support\Facades\Validator;

class AddressController extends BaseController
{
    public function view($id)
    {
        $address = Address::findOrFail($id);
        if ($address->user_id !== Auth::id()) {
            return $this->sendError('Unauthorized.', ['error' => 'You do not have permission to view this address.'], 403);
        }
        return $this->sendResponse(new AddressResource($address), 'Address retrieved successfully.');
    }   
    public function store(AddressRequest $request)
    {
        $data = $request->validated();
        $user_id = Auth::id();
        if (isset($data['is_default']) && $data['is_default']) {
            // Remove default from other addresses of this user
            Address::where('user_id', $user_id)->where('is_default', true)->update(['is_default' => false]);
        }
        $data['user_id'] = $user_id;
        $address = Address::create($data);
        return $this->sendResponse(new AddressResource($address), 'Address created successfully.');
    }

    public function update(AddressRequest $request, $id)
    {
        $address = Address::findOrFail($id);
        if ($address->user_id !== Auth::id()) {
            return $this->sendError('Unauthorized.', ['error' => 'You do not have permission to update this address.'], 403);
        }
        $data = $request->validated();
        if (isset($data['is_default']) && $data['is_default']) {
            Address::where('user_id', $address->user_id)
                ->where('id', '!=', $address->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
        $address->update($data);
        return $this->sendResponse(new AddressResource($address), 'Address updated successfully.');
    }

    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        if ($address->user_id !== Auth::id()) {
            return $this->sendError('Unauthorized.', ['error' => 'You do not have permission to delete this address.'], 403);
        }
        $userAddressCount = Address::where('user_id', $address->user_id)->count();
        if ($userAddressCount <= 1) {
            return $this->sendError('Operation not allowed.', ['error' => 'You must have at least one address.'], 400);
        }
        $address->delete();
        return $this->sendResponse([], 'Address deleted successfully.');
    }
}
