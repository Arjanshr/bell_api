<div class="card">
    @can('add-orders')
    <div class="card-header">
        <a href="{{ route('order.create') }}" class="btn btn-success">Create Order</a>
    </div>
    @endcan
    <div class="card-body">
        <a href="#" class="btn btn-sm {{ $status == 'all' ? 'btn-warning' : 'btn-primary' }}"
            wire:click="allOrders()">All Orders({{ $count['all'] }})</a>
        @foreach ($order_statuses as $order_status)
        <a href="#" class="btn btn-sm {{ $order_status->value == $status ? 'btn-warning' : 'btn-primary' }}"
            wire:click="filterOrders('{{ $order_status->value }}')">{{ str_replace('_', ' ',
            ucfirst($order_status->value)) }}
            ({{ $count[$order_status->value] }})
        </a>
        @endforeach

        <div id="example2_wrapper" class="dataTables_wrapper dt-bootstrap4">
            <div class="row">
                <div class="col-sm-12">
                    <table id="example2" class="table table-bordered table-hover dataTable dtr-inline"
                        aria-describedby="example2_info">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Actions</th>
                                <th>Order By</th>
                                <th width="10%">Shipping Address</th>
                                <th>Order Price</th>
                                <th width="15%">Items</th>
                                <th>Ordered at</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $index => $order)
                            @php
                            $row_class = '';
                            if ($order->is_flagged) {
                            $row_class = 'bg-warning font-weight-bold';
                            }
                            @endphp
                            <tr class="{{ $row_class }}" title="{{ $order->flag_reason ?? '' }}">
                                <td width="20px">{{ $loop->iteration }}</td>
                                <td>
                                    @can('read-orders')
                                    <a href="{{ route('order.show', $order->id) }}" class="btn btn-sm btn-primary"
                                        title="view">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('edit-orders')
                                    <a href="{{ route('order.edit', $order->id) }}" class="btn btn-sm btn-success"
                                        title="Edit">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                    @endcan
                                    @can('delete-orders')
                                    <form method="post" action="{{ route('order.delete', $order->id) }}"
                                        style="display: initial;">
                                        @csrf
                                        @method('delete')
                                        <button class="delete btn btn-danger btn-sm" type="submit" title="Delete"
                                            onclick="">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                                <td>{{ $order->customer ? $order->customer->name : 'Guest' }}</td>
                                <td>{!! $order->shipping_address !!}</td>
                                <td>
                                    {{ number_format($order->grand_total, 2) }}
                                    <br>
                                    <small>
                                        @php
                                        $payment_type = $order->payment_type;
                                        if ($payment_type == 'cash') {
                                        $payment_type = 'Cash On Delivery';
                                        } elseif ($payment_type == 'card') {
                                        $payment_type = 'International Card Payment';
                                        } elseif ($payment_type == 'wallet') {
                                        $payment_type = 'QR Code';
                                        }
                                        @endphp
                                        Payment Type: {{ ucfirst($payment_type ?? 'N/A') }}<br>
                                        Payment: {{ ucfirst($order->payment_status ?? 'N/A') }}<br>
                                        Ref: {{ $order->payment_reference ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    <ul class="mb-0 ps-3">
                                        @foreach ($order->order_items as $item)
                                        <li>{{ $item->product->name }} <span class="badge bg-success badge-circle">{{
                                                $item->quantity }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>{{ $order->created_at->diffForHumans() }}</td>
                                <td>
                                    @can('edit-orders')
                                    <select class="form-control order-status-select" data-order-id="{{ $order->id }}"
                                        data-current-status="{{ $order->status }}"
                                        data-cancel-url="{{ route('order.cancel.store', $order->id) }}"
                                        wire:model="order_status.{{ $order->id }}"
                                        wire:change="change({{ $order->id }})">
                                        @foreach ($order_statuses as $order_status)
                                        @if ($order->status === 'completed')
                                        @if (in_array($order_status->value, ['completed', 'returned']))
                                        <option value="{{ $order_status->value }}">
                                            {{ str_replace('_', ' ', ucfirst($order_status->value)) }}
                                        </option>
                                        @endif
                                        @else
                                        <option value="{{ $order_status->value }}">
                                            {{ str_replace('_', ' ', ucfirst($order_status->value)) }}
                                        </option>
                                        @endif
                                        @endforeach
                                    </select>
                                    @else
                                    {{ str_replace('_', ' ', ucfirst($order->status)) }}
                                    @endcan
                                    @if($order->status === 'cancelled' && $order->cancellation)
                                    <div class="mt-2">
                                        <small class="text-muted">Cancellation:</small>
                                        <div><strong>{{ $order->cancellation->category?->name ?? 'Reason' }}</strong>
                                        </div>
                                        @if($order->cancellation->reason)
                                        <div class="text-wrap">{{ Str::limit($order->cancellation->reason, 200) }}</div>
                                        @endif
                                        <div class="mt-1 text-muted small">
                                            @if($order->cancellation->admin)
                                            By: {{ $order->cancellation->admin->name }}
                                            @endif
                                            @if($order->cancellation->created_at)
                                            &nbsp;•&nbsp;{{ $order->cancellation->created_at->diffForHumans() }}
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Actions</th>
                                <th>Order By</th>
                                <th>Shipping Address</th>
                                <th>Order Price</th>
                                <th>Items</th>
                                <th>Ordered at</th>
                                <th>Status</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Cancellation modal -->
    <div class="modal fade" id="adminCancelModal" tabindex="-1" role="dialog" aria-labelledby="adminCancelModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminCancelModalLabel">Cancel Order</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="adminCancelForm">
                        <input type="hidden" name="order_id" id="cancel_order_id" value="">
                        <div class="mb-3">
                            <label for="order_cancellation_category_id" class="form-label">Reason Category</label>
                            <select name="order_cancellation_category_id" id="order_cancellation_category_id"
                                class="form-control">
                                @foreach(\App\Models\OrderCancellationCategory::where('status',1)->get() as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="cancel_reason" class="form-label">Reason (optional)</label>
                            <textarea name="reason" id="cancel_reason" class="form-control" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="adminCancelConfirm" class="btn btn-danger">Confirm Cancel</button>
                </div>
            </div>
        </div>
    </div>

</div>
@push('css')
<style>
    .select2-selection__rendered {
        line-height: 40px !important;
    }

    .select2-container .select2-selection--single {
        height: 40px !important;
    }

    .select2-selection__arrow {
        height: 40px !important;
    }

    .badge-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        /* equal width & height */
        height: 24px;
        padding: 0;
        /* remove default padding */
        border-radius: 50%;
        /* make it circular */
        font-size: 0.75rem;
        /* smaller text */
        line-height: 1;
    }

    td.order-items {
        max-width: 300px;
        word-break: break-word;
    }
</style>
@endpush

@push('js')
<script>
    (function(){
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            let pendingOrderId = null;
            let pendingCancelUrl = null;

            // Intercept change on selects in capture phase so we can stop Livewire change when needed
            document.addEventListener('change', function(e){
                const el = e.target;
                if (!el.classList || !el.classList.contains('order-status-select')) return;
                const newVal = el.value;
                if (newVal !== 'cancelled') {
                    // update stored current status
                    el.dataset.currentStatus = newVal;
                    return;
                }

                // Prevent Livewire change handler from running for cancellation until confirmed
                e.stopImmediatePropagation();
                e.preventDefault();

                pendingOrderId = el.dataset.orderId;
                pendingCancelUrl = el.dataset.cancelUrl;
                const prev = el.dataset.currentStatus || '';
                // revert select value to previous status until confirmed
                el.value = prev;

                // populate modal
                const orderIdInput = document.getElementById('cancel_order_id');
                if (orderIdInput) orderIdInput.value = pendingOrderId;

                const modalEl = document.getElementById('adminCancelModal');
                if (!modalEl) return;
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    try {
                        window._adminCancelModal = new bootstrap.Modal(modalEl);
                        window._adminCancelModal.show();
                    } catch (err) {
                        // fallback
                        if (window.jQuery) jQuery(modalEl).modal('show');
                    }
                } else if (window.jQuery) {
                    jQuery(modalEl).modal('show');
                } else {
                    // As a last resort ask for confirmation
                    if (confirm('Cancel order #' + pendingOrderId + '?')) {
                        document.getElementById('adminCancelConfirm')?.click();
                    }
                }
            }, true);

            // Use delegation for confirm button since Livewire may re-render DOM
            document.addEventListener('click', async function(e){
                const btn = e.target.closest && e.target.closest('#adminCancelConfirm');
                if (!btn) return;
                e.preventDefault();
                if (!pendingCancelUrl) {
                    if (window.toastr) toastr.error('Cancel URL missing.');
                    return;
                }

                const fd = new FormData();
                fd.append('order_cancellation_category_id', document.getElementById('order_cancellation_category_id')?.value || '');
                fd.append('reason', document.getElementById('cancel_reason')?.value || '');
                fd.append('_token', csrf);

                try {
                    const res = await fetch(pendingCancelUrl, { method: 'POST', body: fd, credentials: 'same-origin' });
                    if (!res.ok) throw new Error('Request failed');
                    // hide modal
                    const modalEl = document.getElementById('adminCancelModal');
                    if (window._adminCancelModal && window._adminCancelModal.hide) window._adminCancelModal.hide();
                    else if (window.jQuery) jQuery(modalEl).modal('hide');

                    if (window.toastr) toastr.success('Order cancelled and reason recorded.');
                    // simple refresh to update Livewire list
                    setTimeout(() => location.reload(), 300);
                } catch (err) {
                    if (window.toastr) toastr.error('Failed to cancel order.');
                }
            });
        })();
</script>
@endpush