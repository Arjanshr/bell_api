<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div style="text-align:center; margin-bottom: 10px;">
            <img src="{{ asset('images/logo.png') }}" alt="Mobile Mandu Logo" style="max-width:180px; height:auto;">
        </div>
        <div style="text-align:center; margin-bottom: 20px;">
            <strong>Mobilemandu Pvt Ltd</strong><br>
            Mid Baneshwor<br>
            Phone Number: 9801104556, 9802352615
        </div>
        <h2 style="text-align:center;">Invoice</h2>
        <table style="width:100%; margin-bottom:20px;">
            <tr>
                <td><strong>Order ID:</strong> {{ $order->id }}</td>
                <td><strong>Date:</strong> {{ $order->created_at->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td><strong>Customer:</strong> {{ $order->customer_name ?? ($order->customer->name ?? '') }}</td>
                <td><strong>Phone:</strong> {{ $order->customer->phone ?? '' }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Email:</strong> {{ $order->customer->email ?? '' }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Shipping Address:</strong> {!! $order->shipping_address !!}</td>
            </tr>
        </table>

        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="border:1px solid #000;">#</th>
                    <th style="border:1px solid #000;">Name</th>
                    <th style="border:1px solid #000;">Quantity</th>
                    <th style="border:1px solid #000;">Rate</th>
                    <th style="border:1px solid #000;">Amount</th>
                    <th style="border:1px solid #000;">Discount</th>
                    <th style="border:1px solid #000;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->order_items as $item)
                <tr>
                    <td style="border:1px solid #000; text-align:center;">{{ $loop->iteration }}</td>
                    <td style="border:1px solid #000;">
                        {{ $item->product->name ?? $item->product_name }}<br>
                        <small style="color:#888;">{{ $item->variant ? $item->variant->sku : 'No Variant' }}</small>
                    </td>
                    <td style="border:1px solid #000; text-align:center;">{{ $item->quantity }}</td>
                    <td style="border:1px solid #000; text-align:right;">Rs {{ number_format($item->price, 2) }}</td>
                    <td style="border:1px solid #000; text-align:right;">Rs {{ number_format($item->quantity * $item->price, 2) }}</td>
                    <td style="border:1px solid #000; text-align:right;">
                        @php
                            $productDiscount = $item->discount ?? 0;
                            $couponDiscount = $item->coupon_discount ?? 0;
                        @endphp
                        @if ($productDiscount && $couponDiscount)
                            <span style="background:#d1ecf1; color:#0c5460; padding:2px 6px; border-radius:3px;">Product: {{ number_format($productDiscount, 2) }}</span><br>
                            <span style="background:#d4edda; color:#155724; padding:2px 6px; border-radius:3px;">Coupon: Rs {{ number_format($couponDiscount, 2) }}</span>
                        @elseif($couponDiscount)
                            <span style="background:#d4edda; color:#155724; padding:2px 6px; border-radius:3px;">Coupon: Rs {{ number_format($couponDiscount, 2) }}</span>
                        @elseif($productDiscount)
                            <span style="background:#d1ecf1; color:#0c5460; padding:2px 6px; border-radius:3px;">Product: {{ number_format($productDiscount, 2) }}</span>
                        @else
                            <span style="color:#888;">0</span>
                        @endif
                    </td>
                    <td style="border:1px solid #000; text-align:right;">Rs {{ number_format($item->quantity * $item->price - ($productDiscount + $couponDiscount), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5"></td>
                    <td style="border:1px solid #000; text-align:right;"><strong>SUBTOTAL</strong></td>
                    <td style="border:1px solid #000; text-align:right;">Rs {{ number_format($order->total_price, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td style="border:1px solid #000; text-align:right;"><strong>DISCOUNT</strong></td>
                    <td style="border:1px solid #000; text-align:right;">Rs {{ number_format($order->discount ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td style="border:1px solid #000; text-align:right;"><strong>SHIPPING FEE</strong></td>
                    <td style="border:1px solid #000; text-align:right;">Rs {{ number_format($order->shipping_price, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td style="border:1px solid #000; text-align:right;"><strong>GRAND TOTAL</strong></td>
                    <td style="border:1px solid #000; text-align:right;">Rs {{ number_format($order->grand_total, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td style="border:1px solid #000; text-align:right;"><b>Total quantity:</b></td>
                    <td style="border:1px solid #000; text-align:right;">{{ $order->order_items->sum('quantity') }}</td>
                </tr>
            </tfoot>
        </table>

        @if ($order->coupon_code)
            <div style="margin-top:20px;">
                <table style="width:100%; border:1px solid #000; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <td colspan="3" style="border:1px solid #000; background:#f8f9fa;"><strong>Discount</strong></td>
                        </tr>
                    </thead>
                    <tr>
                        <td style="border:1px solid #000;">Coupon Discount: Rs {{ number_format($order->coupon_discount, 2) }}</td>
                        <td style="border:1px solid #000;">Coupon Used: {{ $order->coupon_code }}</td>
                        <td style="border:1px solid #000;">Other Discount: Rs {{ number_format($order->other_discount ?? 0, 2) }}</td>
                    </tr>
                </table>
            </div>
        @endif

        <p style="margin-top:30px;">Thank you for your order! We will process it soon.</p>
    </div>
</body>
</html>
