@php
    use Carbon\Carbon;
    $totalAmount = $detail->amount ?? 0; // single service
    $company = \App\Models\CompanyDetails::first();
    $client = $detail->client ?? null;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ $company->company_name }} - Invoice</title>
    <style>
        body { font-family: Arial, Helvetica; font-size: 12px; margin: 0; padding: 20px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .invoice-body { max-width: 794px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; }
        .table td, .table th { padding: 8px; border: 1px solid #dee2e6; }
    </style>
</head>
<body>
<section class="invoice">
    <div class="invoice-body">
        <table>
            <tr>
                <td style="width:50%;">
                    <img src="{{ asset('images/company/' . $company->company_logo) }}" width="120px" />
                </td>
                <td style="width:50%; text-align: right;">
                    <h1 style="font-size:30px; color:blue; margin:0;">INVOICE</h1>
                    @if($detail->bill_paid == 1)
                        <span style="display:inline-block; margin-top:5px; padding:5px 10px; background-color:green; color:white; font-weight:bold; border-radius:5px;">
                            PAID
                        </span>
                    @endif
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="width:40%;">
                    <h5>Bill To</h5>
                    @if($client)
                        <p>{{ $client->business_name }}</p>
                        <p>{{ $client->name }}</p>
                        <p>{{ $client->email }}</p>
                        <p>{{ $client->phone }}</p>
                        <p>{{ $client->address }}</p>
                    @endif
                </td>
                <td style="width:30%;"></td>
                <td style="width:30%; text-align:right;">
                    <p>Date: {{ Carbon::now()->format('d/m/Y') }}</p>
                </td>
            </tr>
        </table>
        <br>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Project</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>VAT %</th>
                    <th>Total (Excl VAT)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center;">1</td>
                    <td style="text-align:center;">{{ $detail->serviceType->name ?? '-' }}</td>
                    <td style="text-align:center;">
                        {{ Carbon::parse($detail->start_date)->format('j F Y') ?? '-' }} - 
                        {{ Carbon::parse($detail->end_date)->format('j F Y') ?? '-' }}
                    </td>
                    <td style="text-align:center;">1</td>
                    <td style="text-align:center;">{{ number_format($detail->amount, 2) ?? '0.00' }}</td>
                    <td style="text-align:center;">0%</td>
                    <td style="text-align:right;">{{ number_format($detail->amount, 2) ?? '0.00' }}</td>
                </tr>
            </tbody>
        </table>

        <table style="margin-top:20px;">
            <tr>
                <td style="width:60%">&nbsp;</td>
                <td style="width:20%; text-align:right; background-color:#f2f2f2;">Total</td>
                <td style="width:20%; text-align:right; background-color:#f2f2f2;">£{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </table>

        @if($client && $client->note)
            <div style="margin-top:30px;">
                <p style="font-weight:bold;">Notes:</p>
                <p>{{ $client->note }}</p>
            </div>
        @endif

        <div style="position: fixed; bottom:0; left:50%; transform:translateX(-50%); max-width:794px; width:100%; padding:10px 20px; border-top:1px solid #ddd; background:white;">
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
</body>
</html>