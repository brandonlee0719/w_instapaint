{literal}
<script>
    window.dataLayer = window.dataLayer || [];
    dataLayer.push({
        'transactionId': '{/literal}{$orderId}{literal}',
        'transactionTotal': {/literal}{$order.order_details.package.total_price}{literal},
        'transactionShipping': {/literal}{$order.order_details.package.shipping_type_price}{literal},
        'frameSizePrice': {/literal}{$order.order_details.package.frame_size_price}{literal},
        'frameTypePrice': {/literal}{$order.order_details.package.frame_type_price}{literal}
    });
</script>
{/literal}
<div class="alert alert-success" role="alert" style="font-size: 20px; text-align: center"><i class="fas fa-check"></i> Thank you for your order. Your payment has been processed. Your invoice number is <a href="/client-dashboard/invoice/{$orderId}/">#{$orderId}</a></div>

<div style="text-align: center; margin-bottom: 15px">
    <img class="order-photo" src="{$order.photo_path}" style="width:90%; max-width: 400px; border-radius: 8px">
</div>

<div style="text-align: center">
    <a href="/client-dashboard/order/{$orderId}/" type="button" class="btn btn-primary btn-lg">View Order Details</a>
</div>
