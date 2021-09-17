{literal}
<style>
    .order {
        border: 1px #ddd solid;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    .order:nth-last-child(2) {
        margin-bottom: 0;
    }
    .order-photo {
        max-width: 100%;
        max-height: 400px;
    }
    .order-header {
        padding: 15px 0;

    }
    .order-body {
        padding: 20px 0;
    }
    .order-header {
        border-bottom: 1px solid lightgrey;
        background-color: #f6f6f6;
        color: #555;
    }
    .order-header > div:last-child {
        text-align: right;
    }
    .order-header-title {
        margin-bottom: 2px;
        text-transform: uppercase;
        font-size: 11px;
    }
    .order-body-title {
        font-size: 16px;
        color: #000;
        text-transform: capitalize;
    }
    .order-body-description {
        margin-bottom: 12px;
    }
    .order-photo-container {
        text-align: center;
    }
    .order-data {
        line-height: 22px;
    }
    .order a:hover {
        text-decoration: underline;
        cursor: pointer;
    }
    #section-header {
        display: none;
    }
    .invoice-title {
        text-align: center;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .print-link {
        text-align: center;
        text-decoration: underline;
        margin-bottom: 30px;
    }
    .invoice-line {
        line-height: 18px;
    }
    .logo {
        max-width: 100px;
    }
    .invoice-table {
        border: 3px solid black;
        width: 100%;
        margin-top: 15px;
    }
    td {
        padding: 5px;
    }
    .table-title {
        font-size: 16px;
        font-weight: bold;
        color: black;
    }
   .invoice-table th {
       text-align: center;
       border-bottom: 3px solid black;
       padding: 5px 0;
    }
    .footer-note {
        text-align: center;
        margin: 10px;
    }
    .footer-note a {
        text-decoration: underline;
    }
    
    @media print {
        a[href]:after {
            content: none !important;
        }
        .logo {
            max-width: 100px !important;
        }
    }
</style>
{/literal}
{if $order && $order.status_name == 'Pending Payment' || $order.status_name == 'Open' || $order.status_name == 'Completed'}
<img class="logo" src="/PF.Site/flavors/material/assets/logos/9cda9e384b9400f3c12256aaa6a81e45.png">

<h1 class="invoice-title">Invoice for Order #{$order.order_id}</h1>
<div class="print-link" ><a href="" onclick="javascript:window.print();">Print this page for your records.</a></div>

<div class="invoice-line">
    <strong>Order Placed:</strong> {$order.created_date}
</div>

<div class="invoice-line">
    <strong>Instapaint order number:</strong> {$order.order_id}
</div>

<div class="invoice-line">
    <strong>Order Total: {$order.price_with_discount_formatted}</strong>
</div>


<table class="invoice-table">
    <thead>
        <th>
            <span class="table-title">Shipping Address</span>
        </th>
    </thead>
    <tbody>
    <tr>
        <td>
            <div class="order-data">
                {$order.order_details.shipping_address.full_name}<br>
                {$order.order_details.shipping_address.street_address}<br>
                {if $order.order_details.shipping_address.street_address_2}{$order.order_details.shipping_address.street_address_2}<br>{/if}
                {$order.order_details.shipping_address.city}, {$order.order_details.shipping_address.state_province_region} {$order.order_details.shipping_address.zip_code}<br>
                {$order.order_details.shipping_address.country_name}<br>
            </div>
        </td>
    </tr>
    </tbody>
</table>

<table class="invoice-table">
    <thead>
    <th>
        <span class="table-title">Order Summary</span>
    </th>
    </thead>
    <tbody>
    <tr>
        <td>
            <div class="order-data">

                <span id="summary-frame-size">Frame Size:</span> {$order.order_details.package.frame_size_name} ({$order.order_details.package.frame_size_description}):<span class="pull-right" id="summary-frame-size-price">{$order.frame_size_price_formatted}</span><br>
                <span id="summary-frame-type">Frame Type:</span> {$order.order_details.package.frame_type_name} ({$order.order_details.package.frame_type_description}):<span class="pull-right" id="summary-frame-type-price">{$order.frame_type_price_formatted}</span><br>
                <span id="summary-shipping-type">Shipping Type:</span> {$order.order_details.package.shipping_type_name} ({$order.order_details.package.shipping_type_description}):<span class="pull-right" id="summary-shipping-type-price">{$order.shipping_type_price_formatted}</span><br>
                <span id="summary-faces-line"><span id="summary-faces">Faces ({$order.faces}):</span><span class="pull-right" id="summary-faces-price">{$order.faces_price_formatted}</span><br></span>
                <span id="summary-style-line"><span id="summary-style">Style ({$order.order_details.style.name}):</span><span class="pull-right" id="summary-style-price">${$order.order_details.style.price_str}</span><br></span>
                {if $order.is_expedited == '1'}<span id="summary-expedited-service-line"><span id="summary-expedited-service">Expedited Service:</span><span class="pull-right" id="summary-expedited-service-price">$50.00</span><br></span>{/if}
                {if $order.order_details.discount}
                <span id="discount-line"><span id="summary-discount">Discount: {$order.order_details.discount.name} ({$order.order_details.discount.discount_percentage}% Off):</span><span class="pull-right" id="summary-discount-price">-{$order.discount_price_formatted}</span><br></span>
                {/if}
                <span class="pull-right">-----</span><br>
                <span id="summary-total"><strong>Grand Total:</strong></span><span class="pull-right"><strong id="summary-total-price">{$order.price_with_discount_formatted}</strong></span>
            </div>
        </td>
    </tr>
    </tbody>
</table>

{if $order.order_status_id == 1}
<table class="invoice-table">
    <thead>
    <th>
        <span class="table-title">Payment Information</span>
    </th>
    </thead>
    <tbody>
    <tr>
        <td>
            <div class="order-data">

                <span id="summary-frame-size">Payment Status:</span><span class="pull-right" id="summary-frame-size-price">Pending</span><br>
            </div>
        </td>
    </tr>
    </tbody>
</table>
{else}
<table class="invoice-table">
    <thead>
    <th>
        <span class="table-title">Payment Information</span>
    </th>
    </thead>
    <tbody>
    <tr>
        <td>
            <div class="order-data">

                <span id="summary-frame-size">Payment Status:</span><span class="pull-right" id="summary-frame-size-price">Paid</span><br>
            </div>
        </td>
    </tr>
    </tbody>
</table>
{/if}

<div class="footer-note">
    To view the status of your order, return to <a href="/client-dashboard/order/{$order.order_id}/">Order Details</a>.
</div>

{else}
<div class="alert alert-danger" role="alert">The invoice you are trying to view does not exist.</div>
{/if}
