@extends('admin.master')

@section('content')
<div class="grid kpis">
    <div class="card">
        <h3>Total Clients</h3>
        <div class="metric mono">12</div>
    </div>
    <div class="card">
        <h3>Active Projects</h3>
        <div class="metric mono">7</div>
    </div>
    <div class="card">
        <h3>Ongoing Services</h3>
        <div class="metric mono">9</div>
    </div>
    <div class="card">
        <h3>Pending Dues</h3>
        <div class="metric mono">£4,190</div>
    </div>
</div>
<div class="split" style="margin-top: 14px;">
    <div class="card">
        <h3>Upcoming Deadlines</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>When</th>
                    <th>Item</th>
                    <th>Client</th>
                    <th class="right">Due</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Mon 18 Aug</td>
                    <td>SEO monthly report</td>
                    <td>Orbit Media</td>
                    <td class="right mono">—</td>
                </tr>
                <tr>
                    <td>Wed 20 Aug</td>
                    <td>Invoice #1029</td>
                    <td>Nova Retail</td>
                    <td class="right mono">£620</td>
                </tr>
                <tr>
                    <td>Fri 22 Aug</td>
                    <td>Hosting renewal</td>
                    <td>BluePeak</td>
                    <td class="right mono">£120</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card">
        <h3>Calendar (This Month)</h3>
        <div id="calHead" style="font-size: 12px; color: var(--muted); margin: 4px 0 8px;">August 2025</div>
        <div class="calendar" id="calendar"></div>
    </div>
</div>
@endsection