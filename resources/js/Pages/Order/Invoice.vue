<template>
    <AuthLayout>
        <!-- Page header -->
        <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Invoice</h1>
                <p class="text-sm text-gray-500 mt-0.5">Order #{{ data.no?.substring(0, 8) }}…</p>
            </div>
            <button
                @click="print"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm"
            >
                <i class="fas fa-print text-xs"></i> Print Invoice
            </button>
        </div>

        <!-- Invoice card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <!-- Printable area -->
            <div id="print-div">

                <!-- Invoice header -->
                <div class="p-6 border-b border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">

                        <!-- Left: invoice meta -->
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 rounded-lg bg-green-600 flex items-center justify-center">
                                    <i class="fas fa-file-invoice text-white text-xs"></i>
                                </div>
                                <span class="text-lg font-extrabold text-gray-900">Invoice</span>
                                <span class="text-lg font-extrabold text-green-600">#{{ invoice_no }}</span>
                            </div>
                            <div class="text-xs text-gray-500 flex items-center gap-2">
                                <span class="font-medium text-gray-600 w-24">Order No.</span>
                                <span class="font-mono text-gray-700 text-[11px] break-all">{{ data.no }}</span>
                            </div>
                            <div class="text-xs text-gray-500 flex items-center gap-2">
                                <span class="font-medium text-gray-600 w-24">Order Date</span>
                                <span class="text-gray-700">{{ data.created_at ? data.created_at.replace('T', ' ').substring(0, 10) : '—' }}</span>
                            </div>
                            <div class="text-xs text-gray-500 flex items-center gap-2">
                                <span class="font-medium text-gray-600 w-24">Email</span>
                                <span class="text-gray-700">{{ data.user.email }}</span>
                            </div>
                        </div>

                        <!-- Right: customer info -->
                        <div class="bg-gray-50 rounded-xl border border-gray-100 px-5 py-4 space-y-1.5 min-w-[200px]">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Bill To</p>
                            <p class="font-bold text-gray-800 text-sm">{{ data.user.name }}</p>
                            <p class="text-xs text-gray-500 flex items-start gap-1.5">
                                <i class="fas fa-location-dot text-[10px] mt-0.5 text-gray-400"></i>
                                {{ data.address || 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-500 flex items-center gap-1.5">
                                <i class="fas fa-phone text-[10px] text-gray-400"></i>
                                {{ data.user.phone_no || 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Items table -->
                <div class="px-6 py-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-3">Ordered Items</p>

                    <!-- Desktop table -->
                    <div class="hidden sm:block rounded-xl overflow-hidden border border-gray-100">
                        <table class="w-full text-sm border-collapse">
                            <thead>
                                <tr class="bg-green-600 text-white">
                                    <th class="text-left px-4 py-3 font-semibold text-xs uppercase tracking-wide">Product</th>
                                    <th class="text-right px-4 py-3 font-semibold text-xs uppercase tracking-wide">Price</th>
                                    <th class="text-right px-4 py-3 font-semibold text-xs uppercase tracking-wide">Qty</th>
                                    <th class="text-right px-4 py-3 font-semibold text-xs uppercase tracking-wide">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(d, i) in data.details"
                                    :key="d.id"
                                    :class="i % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                                >
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ d.product.name }}</td>
                                    <td class="px-4 py-3 text-right text-gray-600">₱{{ numberWithCommas(d.product.price) }}</td>
                                    <td class="px-4 py-3 text-right text-gray-600">{{ d.qty }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-green-600">₱{{ numberWithCommas(d.grandtotal) }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="bg-green-50 border-t-2 border-green-200">
                                    <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-700 text-sm">Grand Total</td>
                                    <td class="px-4 py-3 text-right font-extrabold text-green-700 text-base">
                                        ₱{{ numberWithCommas(data.details.reduce((t, v) => v.grandtotal + t, 0)) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Mobile cards -->
                    <div class="sm:hidden space-y-2">
                        <div
                            v-for="d in data.details"
                            :key="d.id"
                            class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3 border border-gray-100"
                        >
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ d.product.name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">₱{{ numberWithCommas(d.product.price) }} × {{ d.qty }}</p>
                            </div>
                            <p class="font-bold text-green-600 text-sm">₱{{ numberWithCommas(d.grandtotal) }}</p>
                        </div>
                        <!-- Grand total row -->
                        <div class="flex items-center justify-between bg-green-50 rounded-xl px-4 py-3 border border-green-100">
                            <p class="font-bold text-gray-700 text-sm">Grand Total</p>
                            <p class="font-extrabold text-green-700 text-base">
                                ₱{{ numberWithCommas(data.details.reduce((t, v) => v.grandtotal + t, 0)) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Payment summary -->
                <div class="px-6 pb-6">
                    <div class="bg-gray-50 rounded-xl border border-gray-100 p-4 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-8">
                        <div class="flex items-center gap-3">
                            <div :class="`w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 ${data.paid === 1 ? 'bg-green-100' : 'bg-red-100'}`">
                                <i :class="`fas ${data.paid === 1 ? 'fa-check text-green-600' : 'fa-clock text-red-500'} text-sm`"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Payment Status</p>
                                <p :class="`font-bold text-sm ${data.paid === 1 ? 'text-green-600' : 'text-red-500'}`">
                                    {{ data.paid === 1 ? 'Paid' : 'Unpaid' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-credit-card text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Payment Mode</p>
                                <p class="font-bold text-sm text-gray-800">{{ data.payment_method }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /print-div -->
        </div>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';

const { data, invoice_no } = defineProps(['data', 'invoice_no']);

data.details.map(d => {
    d.grandtotal = parseInt(d.product.price) * parseInt(d.qty);
});

const numberWithCommas = (str) => {
    str += '';
    const number = parseFloat(str.replace(/,/g, ''));
    return !isNaN(number) ? number.toLocaleString() : '';
};

function print() {
    const tab = window.open('', '', 'left=0,top=0,width=860,height=900,toolbar=0,scrollbars=0,status=0');
    tab.document.open();
    tab.document.write(`<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #${invoice_no}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #1f2937; background: #fff; padding: 32px; }

        .header { display: flex; justify-content: space-between; gap: 24px; margin-bottom: 24px; }
        .invoice-title { font-size: 22px; font-weight: 800; color: #16a34a; margin-bottom: 12px; }
        .meta-row { display: flex; gap: 12px; font-size: 12px; color: #6b7280; margin-bottom: 4px; }
        .meta-label { font-weight: 600; color: #374151; width: 90px; flex-shrink: 0; }

        .bill-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; min-width: 200px; }
        .bill-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; margin-bottom: 8px; }
        .bill-name { font-weight: 700; font-size: 14px; color: #111827; margin-bottom: 4px; }
        .bill-detail { font-size: 12px; color: #6b7280; margin-bottom: 3px; }

        .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; margin-bottom: 10px; }

        table { width: 100%; border-collapse: collapse; border-radius: 10px; overflow: hidden; border: 1px solid #e5e7eb; }
        thead tr { background: #16a34a; color: white; }
        th { padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        th:first-child { text-align: left; }
        th:not(:first-child) { text-align: right; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        td { padding: 10px 14px; font-size: 13px; border-bottom: 1px solid #f3f4f6; }
        td:first-child { font-weight: 600; color: #111827; }
        td:not(:first-child) { text-align: right; color: #4b5563; }
        tfoot tr { background: #f0fdf4; border-top: 2px solid #bbf7d0; }
        tfoot td { padding: 12px 14px; font-weight: 800; font-size: 14px; }
        tfoot td:last-child { color: #15803d; text-align: right; }
        tfoot td:not(:last-child) { text-align: right; color: #374151; }

        .payment-row { display: flex; gap: 32px; margin-top: 20px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px 18px; }
        .pay-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; margin-bottom: 3px; }
        .pay-value { font-weight: 700; font-size: 14px; }
        .paid { color: #16a34a; }
        .unpaid { color: #dc2626; }

        @media print {
            @page { size: auto; margin: 10mm; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="invoice-title">Invoice #${invoice_no}</div>
            <div class="meta-row"><span class="meta-label">Order No.</span><span style="font-family:monospace;font-size:11px;">${data.no}</span></div>
            <div class="meta-row"><span class="meta-label">Order Date</span><span>${data.created_at ? data.created_at.replace('T', ' ').substring(0, 10) : '—'}</span></div>
            <div class="meta-row"><span class="meta-label">Email</span><span>${data.user.email}</span></div>
        </div>
        <div class="bill-box">
            <div class="bill-title">Bill To</div>
            <div class="bill-name">${data.user.name}</div>
            <div class="bill-detail">📍 ${data.address || 'N/A'}</div>
            <div class="bill-detail">📞 ${data.user.phone_no || 'N/A'}</div>
        </div>
    </div>

    <div class="section-title">Ordered Items</div>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            ${data.details.map(d => `
            <tr>
                <td>${d.product.name}</td>
                <td style="text-align:right">₱${Number(d.product.price).toLocaleString()}</td>
                <td style="text-align:right">${d.qty}</td>
                <td style="text-align:right;font-weight:700;color:#16a34a">₱${Number(d.grandtotal).toLocaleString()}</td>
            </tr>`).join('')}
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right">Grand Total</td>
                <td>₱${Number(data.details.reduce((t, v) => v.grandtotal + t, 0)).toLocaleString()}</td>
            </tr>
        </tfoot>
    </table>

    <div class="payment-row">
        <div>
            <div class="pay-label">Payment Status</div>
            <div class="pay-value ${data.paid === 1 ? 'paid' : 'unpaid'}">${data.paid === 1 ? '✓ Paid' : '⏳ Unpaid'}</div>
        </div>
        <div>
            <div class="pay-label">Payment Mode</div>
            <div class="pay-value">${data.payment_method}</div>
        </div>
    </div>
</body>
</html>`);
    tab.print();
    tab.close();
}
</script>
