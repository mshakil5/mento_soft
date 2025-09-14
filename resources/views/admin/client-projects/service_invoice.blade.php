<!DOCTYPE html>
<html lang="en">
<head>
    @php
        use Carbon\Carbon;
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company->company_name }} - Invoice</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        p {
            margin: 0;
            padding: 0;
        }
        .invoice-body {
            max-width: 794px;
            margin: 0 auto;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        .table td, .table th { padding: 8px; border: 1px solid #dee2e6; }
        .no-print { display: none; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <section class="invoice">
        <div class="invoice-body">
            <br>
            <br>
            <table>
                <tr>
                    <td style="width:50%; text-align:left;">
                        <img src="{{ asset('images/company/' . $company->company_logo) }}" width="120px" />
                    </td>
                    <td style="width:50%; text-align:right;">
                        <h1 style="font-size: 30px; color:blue; margin: 0;">INVOICE</h1>
                    </td>
                </tr>
            </table>

            <br><br>

            @php
                $client = $services->first()->client;
            @endphp
            <table>
                <tr>
                    <td style="width:60%; vertical-align: top;">
                        <h3 style="margin-bottom: 2px;">Bill To</h3>
                        @if ($client->business_name) <p>{{ $client->business_name }}</p> @endif
                        @if ($client->email) <p>{{ $client->email }}</p> @endif
                        @if ($client->phone) <p>{{ $client->phone }}</p> @endif
                        @if ($client->address) <p>{{ $client->address }}</p> @endif
                    </td>
                    <td style="width:40%; text-align:right; vertical-align: top;">
                        <p>Date: {{ Carbon::now()->format('d/m/Y') }}</p>
                        <p>Invoice #: INV{{ str_pad($services->first()->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </td>
                </tr>
            </table>

            <br>

            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Service</th>
                        <th class="text-center">Period</th>
                        <th class="text-center">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subtotal = 0; @endphp
                    @foreach ($services as $index => $service)
                        @php 
                            $subtotal += $service->amount; 
                            $dateRange = $service->start_date && $service->end_date 
                                ? Carbon::parse($service->start_date)->format('d M Y') . ' - ' . Carbon::parse($service->end_date)->format('d M Y') 
                                : '';
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">
                                <strong>{{ $service->project?->title }}</strong> {{ $service->serviceType->name }}
                            </td>
                            <td class="text-center">{{ $dateRange }}</td>
                            <td class="text-center">£{{ number_format($service->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table style="margin-top:20px;">
                <tr>
                    <td style="width:70%"></td>
                    <td style="width:30%">
                        <table style="width:100%;">
                            <tr>
                                <td>Subtotal</td>
                                <td class="text-right">£{{ number_format($subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td>VAT</td>
                                <td class="text-right">£0.00</td>
                            </tr>
                            <tr>
                                <td style="background:#f2f2f2"><b>Total</b></td>
                                <td class="text-right" style="background:#f2f2f2"><b>£{{ number_format($subtotal, 2) }}</b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            @if ($services->first()->note)
                <div>
                    <p>{{ $services->first()->note }}</p>
                </div>
            @endif

            <div style="position: fixed; bottom: 0; left: 50%; transform: translateX(-50%);
                        max-width: 794px; width: 100%; padding: 10px 20px;
                        border-top: 1px solid #ddd; background: white;">
                <table>
                    <tr>
                        <td style="width:50%; text-align:left;">
                            <b>{{ $company->business_name ?: $company->company_name }}</b><br>
                            Registration Number: {{ $company->company_reg_number ?? '' }}<br>
                            Vat Number: {{ $company->vat_number ?? '' }}<br>
                            {{ $company->address1 ?? '' }}
                        </td>
                        <td style="width:50%; text-align:right;">
                            {{ $company->phone1 ?? '' }} <br>
                            {{ $company->email1 ?? '' }} <br>
                            {{ $company->website ?? '' }} <br>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </section>

    <script>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 1000);
        };
    </script>
</body>
</html>