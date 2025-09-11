<!DOCTYPE html>
<html lang="en">
<head>
      @php
          $company = \App\Models\CompanyDetails::first();
          use Carbon\Carbon;
      @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ $company->company_name }} - Invoice</title>
    <style>
        body {
            font-family: Arial, Helvetica;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .invoice-body {
            max-width: 794px;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .table td, .table th {
            padding: 8px;
        }
        .no-print {
            display: none;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <section class="invoice">
        <div class="invoice-body">
            <table>
                <tbody>
                    <tr>
                        <td style="width:50%;">
                            <div style="text-align: left;">
                                <img src="{{ asset('images/company/' . $company->company_logo) }}" width="120px" style="display:inline-block;" />
                            </div>
                        </td>
                        <td style="width:50%;">
                            <div style="text-align: right;">
                                <h1 style="font-size: 30px; color:blue; margin: 0;">INVOICE</h1>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <br><br>

            <table>
                <tbody>
                    <tr>
                        <td style="width:40%;">
                            <div>
                                <h5 style="font-size: 12px; margin: 5px;text-align: left; line-height: 10px;">Bill To</h5>
                                @if ($invoice->client->business_name) 
                                <p style="font-size: 12px; margin: 5px;text-align: left; line-height: 10px;">{{ $invoice->client->business_name }}</p>
                                @endif
                                @if ($invoice->client->email) 
                                <p style="font-size: 12px; margin: 5px;text-align: left; line-height: 10px;">{{ $invoice->client->email }}</p>
                                @endif
                                @if ($invoice->client->phone)    
                                <p style="font-size: 12px; margin: 5px;text-align: left; line-height: 10px;">{{ $invoice->client->phone }}</p>
                                @endif
                                @if ($invoice->client->address)
                                <p style="font-size: 12px; margin: 5px; text-align: left; line-height: 10px;">
                                    {{ $invoice->client->address }}
                                </p>
                                @endif                              
                            </div>
                        </td>
                        <td style="width:30%;"></td>
                        <td style="width:30%;">
                            <div style="text-align: right;">
                                <p style="font-size: 12px; margin: 5px;text-align: right;line-height: 10px;">Invoice No: {{ $invoice->invoice_number }}</p>
                                <p style="font-size: 12px; margin: 5px;text-align: right;line-height: 10px;">Date: {{ Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>

            <div class="row overflow">
                <table class="table" style="border: 1px solid #dee2e6;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #dee2e6; text-align:center;">#</th>
                            <th style="border: 1px solid #dee2e6; text-align:center;">Project</th>
                            <th style="border: 1px solid #dee2e6; text-align:center;">Description</th>
                            <th style="border: 1px solid #dee2e6; text-align:center;">Qty</th>
                            <th style="border: 1px solid #dee2e6; text-align:center;">Price</th>
                            <th style="border: 1px solid #dee2e6; text-align:center;">VAT %</th>
                            <th style="border: 1px solid #dee2e6; text-align:right;">Total (Excl VAT)</th>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach($invoice->details as $index => $item)
                        <tr>
                          <td style="border: 1px solid #dee2e6; text-align:center;">{{ $index + 1 }}</td>
                          <td style="border: 1px solid #dee2e6; text-align:center;">{{ $item->project_name }}</td>
                          <td style="border: 1px solid #dee2e6; text-align:center;">{{ $item->description }}</td>
                          <td style="border: 1px solid #dee2e6; text-align:center;">{{ $item->qty }}</td>
                          <td style="border: 1px solid #dee2e6; text-align:center;">{{ number_format($item->unit_price, 2) }}</td>
                          <td style="border: 1px solid #dee2e6; text-align:center;">{{ number_format($item->vat_percent, 0) }}%</td>
                          <td style="border: 1px solid #dee2e6; text-align:right;">{{ number_format($item->total_exc_vat, 2) }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                </table>

                <table style="margin-top: 20px;">
                    <tbody>
                        <tr>
                            <td style="width: 20%">&nbsp;</td>
                            <td style="width: 25%">&nbsp;</td>
                            <td style="width: 25%">&nbsp;</td>
                            <td>Subtotal</td>
                            <td style="text-align:right">£{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        @if($invoice->discount_percent)
                        <tr>
                            <td style="width: 20%">&nbsp;</td>
                            <td style="width: 25%">&nbsp;</td>
                            <td style="width: 25%">&nbsp;</td>
                            <td>Discount ({{ $invoice->discount_percent }}%)</td>
                            <td style="text-align:right">£{{ number_format($invoice->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($invoice->vat_amount)
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>VAT</td>
                            <td style="text-align:right">£{{ number_format($invoice->vat_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td></td>
                            <td></td>
                            <td>&nbsp;</td>
                            <td style="background-color: #f2f2f2">Total</td>
                            <td style="text-align:right; background-color: #f2f2f2">£{{ number_format($invoice->net_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if ($invoice->description)
            <div style="margin-top: 30px;">
                <p style="font-weight: bold; margin-bottom: 5px;">Notes:</p>
                <p style="margin: 0;">{{ $invoice->description }}</p>
            </div>
            @endif
            <div style="position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); max-width: 794px; width: 100%; padding: 10px 20px; border-top: 1px solid #ddd; background: white;">

                <table>
                    <tbody>
                        <tr>
                            <td style="width: 50%; text-align:left;">
                                <b>{{ $company->business_name ? $company->business_name : $company->company_name }}</b><br>
                                Registration Number: {{ $company->company_reg_number ?? '' }}<br>
                                Vat Number: {{ $company->vat_number ?? '' }}<br>
                                {{ $company->address1 ?? '' }}
                            </td>
                            <td style="width: 50%; text-align:right;">
                                {{ $company->phone1 ?? '' }} <br>
                                {{ $company->email1 ?? '' }} <br>
                                {{ $company->website ?? '' }} <br>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        };
    </script>
</body>
</html>