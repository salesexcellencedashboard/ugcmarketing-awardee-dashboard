<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<script>
    window.dashboardData = null;
    window.dashboardKPIs = null;
    window.hasDashboardData = false;
</script>

<style>
@keyframes fadeIn { from { opacity:0; transform:scale(0.95); } to { opacity:1; transform:scale(1); } }
.view-section { display: none; }
.view-section.active { display: flex; flex-direction: column; flex: 1; min-height: 0; }
.view-section.charts-section { position:absolute; left:-9999px; top:0; width:100%; height:auto; display:flex; flex-direction:column; flex:1; visibility:hidden; pointer-events:none; opacity:0; }
.view-section.charts-section.active { position:static; left:auto; top:auto; visibility:visible; pointer-events:all; opacity:1; }
.dashboard-container { display:flex; flex-direction:column; flex:1; min-height:0; }
.dashboard-container:not(.active) { position:absolute; left:-9999px; top:0; width:100%; visibility:hidden; pointer-events:none; }
.data-tables-grid { display:grid; grid-template-columns:1fr 1fr; grid-template-rows:1fr 1fr; gap:10px; flex:1; min-height:0; }
.data-tables-grid.filtered { grid-template-columns:1fr; grid-template-rows:1fr; }
#primeSpandrelDash .data-tables-grid { grid-template-rows:1fr 1fr; }
#primeSpandrelDash .data-tables-grid.filtered { grid-template-rows:1fr; }
.charts-grid { display:grid; grid-template-columns:1fr 1fr; grid-template-rows:1fr 1fr; gap:12px; flex:1; min-height:0; padding:4px; }
.charts-grid.filtered { grid-template-columns:1fr; grid-template-rows:1fr; }
.data-tables-grid.filtered .panel-card,
.charts-grid.filtered .panel-card { grid-column:1; grid-row:1; }
.data-tables-grid .panel-card, .charts-grid .panel-card { border-radius:12px; border:1px solid #eef0f4; height:100%; min-height:0; display:flex; flex-direction:column; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.04); overflow:hidden; }
.kpi-row { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin-bottom:8px; flex-shrink:0; }
.kpi-card { background:#fff; border-radius:10px; padding:0.5rem 0.7rem; border:1px solid #eef0f4; box-shadow:0 1px 4px rgba(0,0,0,0.03); }
.kpi-label { font-size:0.55rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.4px; margin-bottom:2px; }
.kpi-value { font-size:0.95rem; font-weight:800; color:#1e293b; }
.table-header-section { background:#6b7280 !important; padding:0.5rem 0.75rem; display:flex; justify-content:space-between; align-items:center; flex-shrink:0; border-bottom:2px solid #4b5563; }
.table-title { font-size:0.75rem; font-weight:800; color:#fff; text-transform:uppercase; letter-spacing:0.5px; }
.table-category-filter { font-size:0.55rem !important; font-weight:600 !important; color:#fff !important; background:rgba(255,255,255,0.12) !important; border:1px solid rgba(255,255,255,0.2) !important; padding:0.2rem 0.5rem !important; border-radius:20px !important; cursor:pointer !important; }
.data-tables-grid .panel-body { flex:1; min-height:0; padding:0.35rem 0.5rem; display:flex; flex-direction:column; overflow:auto; }
.data-table-add-btn { background:#f8fafc !important; color:#475569 !important; border:1px solid #cbd5e1 !important; border-radius:4px !important; padding:0.3rem 0.5rem !important; font-size:0.6rem !important; font-weight:800 !important; text-transform:uppercase !important; letter-spacing:0.3px !important; cursor:pointer !important; transition:all 0.15s ease !important; display:inline-flex !important; align-items:center !important; gap:3px !important; }
.data-table-add-btn:hover { background:#e2e8f0 !important; border-color:#94a3b8 !important; }
.rank-gold { background:linear-gradient(135deg,#fffbeb,#fef3c7) !important; }
.rank-gold td { color:#92400e; font-weight:700; font-size:0.72rem; }
.rank-silver { background:linear-gradient(135deg,#f8fafc,#e5e7eb) !important; }
.rank-silver td { color:#374151; font-weight:600; font-size:0.7rem; }
.rank-bronze { background:linear-gradient(135deg,#fff7ed,#fed7aa) !important; }
.rank-bronze td { color:#78350f; font-weight:600; font-size:0.68rem; }
.growth-up { color:#059669 !important; font-weight:700; }
.growth-down { color:#dc2626 !important; font-weight:700; }
.analytics-table { width:100%; table-layout:fixed; border-collapse:collapse; font-size:0.75rem; height:100%; border:1px solid #d1d5db; }
.analytics-table th { padding:0.5rem 0.35rem; text-align:center; border:1px solid #cbd5e1; font-size:0.65rem; text-transform:uppercase; color:#475569; font-weight:800; letter-spacing:0.3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; background:#f8fafc; }
.analytics-table td { padding:0.55rem 0.35rem; border:1px solid #e2e8f0; text-align:center; font-size:0.65rem; color:#334155; vertical-align:middle; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.data-tables-grid .tb-analytics-table { width:100%; table-layout:fixed; border-collapse:collapse; font-size:0.78rem; border:1px solid #d1d5db; }
.data-tables-grid .tb-analytics-table th { padding:0.35rem 0.2rem; text-align:center; border:1px solid #cbd5e1; font-size:0.62rem; text-transform:uppercase; color:#475569; font-weight:800; letter-spacing:0.3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; background:#f8fafc; }
.data-tables-grid .tb-analytics-table td { padding:0.4rem 0.25rem; border:1px solid #e2e8f0; text-align:center; font-size:0.68rem; color:#334155; vertical-align:middle; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.elite-table-card .analytics-table { width:100%; border-collapse:collapse; font-size:0.72rem; border:1px solid #d1d5db; }
.elite-table-card .analytics-table thead { background:#f8fafc; position:sticky; top:0; z-index:2; }
.elite-table-card .analytics-table th { padding:0.5rem 0.5rem; text-align:center; border:1px solid #cbd5e1; font-size:0.62rem; text-transform:uppercase; color:#475569; font-weight:800; letter-spacing:0.3px; white-space:nowrap; background:#f8fafc; }
.elite-table-card .analytics-table td { padding:0.7rem 0.5rem; border:1px solid #e2e8f0; text-align:center; font-size:0.68rem; color:#334155; vertical-align:middle; }
.elite-circle-layout { display:flex; flex-direction:row; gap:10px; flex:1; min-height:0; }
.elite-chart-col { flex:1.2; min-height:0; display:flex; flex-direction:column; }
.elite-tables-col { flex:0.8; min-height:0; display:flex; flex-direction:column; gap:10px; }
.elite-chart-card { flex:1; display:flex; flex-direction:column; border-radius:12px; border:1px solid #eef0f4; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.04); overflow:hidden; }
.elite-table-card { flex:1; display:flex; flex-direction:column; border-radius:12px; border:1px solid #eef0f4; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.04); overflow:hidden; }
.elite-table-card .table-header-section { flex-shrink:0; padding:0.55rem 0.75rem; background:#6b7280 !important; }
.elite-table-card .panel-body { flex:1; overflow:auto; padding:0; display:flex; flex-direction:column; }
.chart-half { flex:1; display:flex; flex-direction:column; min-width:0; padding:0; }
.chart-half .panel-body { flex:1; padding:2px; display:flex; flex-direction:column; min-height:0; }
.chart-label { font-size:0.5rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.3px; margin-bottom:0; flex-shrink:0; text-align:center; line-height:1.2; }
.apex-chart-wrap { flex:1; display:flex; align-items:center; justify-content:center; min-height:0; width:100%; overflow:hidden; }
.apex-chart-wrap > div { width:100% !important; height:100% !important; min-height:120px; }
.charts-grid .panel-card .row.g-0 { display:flex; flex-direction:row; flex:1; min-height:0; }
.charts-grid .panel-card .row.g-0 > div { flex:1; display:flex; flex-direction:column; min-width:0; }
.view-toggle { display:flex; gap:6px; flex-wrap:wrap; }
.view-btn { padding:0.4rem 0.8rem; border:1.5px solid #d1d5db; background:#fff; color:#6b7280; font-weight:700; font-size:0.7rem; cursor:pointer; border-radius:8px; transition:all 0.2s ease; white-space:nowrap; }
.view-btn:hover { background:#f3f4f6; border-color:#9ca3af; }
.view-btn.active { background:#374151; color:#fff; border-color:#374151; }
@media (max-width:992px){ .elite-circle-layout{flex-direction:column} .elite-chart-col{flex:none;min-height:350px} .elite-tables-col{flex:none;min-height:500px} }
.de-section { margin-top:10px; flex-shrink:0; border:1px solid #e5e7eb; border-radius:10px; background:#fff; overflow:hidden; }
.de-section-header { display:flex; justify-content:space-between; align-items:center; padding:0.5rem 0.75rem; background:#f8fafc; border-bottom:2px solid #059669; cursor:pointer; }
.de-section-header .de-title { font-size:0.75rem; font-weight:800; color:#059669; text-transform:uppercase; letter-spacing:0.4px; }
.de-section-body { display:block; max-height:400px; overflow:auto; }
.de-section-body.closed { display:none; }
.de-section-body table { width:100%; border-collapse:collapse; font-size:0.68rem; }
.de-section-body th { padding:0.4rem 0.3rem; text-align:center; border:1px solid #d1d5db; font-size:0.58rem; text-transform:uppercase; color:#475569; font-weight:800; letter-spacing:0.2px; background:#f1f5f9; position:sticky; top:0; z-index:1; white-space:nowrap; }
.de-section-body td { padding:0.35rem 0.25rem; border:1px solid #e2e8f0; text-align:center; font-size:0.6rem; color:#334155; vertical-align:middle; white-space:nowrap; }
.de-section-body tr:hover { background:#f8fafc; }
.de-btn { padding:0.3rem 0.6rem; border:none; border-radius:4px; font-size:0.62rem; font-weight:700; cursor:pointer; transition:all 0.15s ease; text-transform:uppercase; letter-spacing:0.2px; display:inline-flex; align-items:center; gap:3px; }
.de-btn-primary { background:#059669; color:#fff; }
.de-btn-primary:hover { background:#047857; }
.de-btn-edit { background:#3b82f6; color:#fff; }
.de-btn-edit:hover { background:#2563eb; }
.de-btn-delete { background:#ef4444; color:#fff; }
.de-btn-delete:hover { background:#dc2626; }
.de-btn-export { background:#374151; color:#fff; }
.de-btn-export:hover { background:#1f2937; }
.de-btn-add { background:#059669; color:#fff; }
.de-btn-add:hover { background:#047857; }
.de-btn-reset { background:#ef4444; color:#fff; }
.de-btn-reset:hover { background:#dc2626; }
.de-empty { padding:1.5rem; text-align:center; color:#94a3b8; font-size:0.75rem; }
.de-modal-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:99999; justify-content:center; align-items:center; padding:20px; }
.de-modal-overlay.show { display:flex; }
.de-modal { background:#fff; border-radius:12px; width:90%; max-width:800px; max-height:90vh; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,0.3); animation:modalFadeIn 0.2s ease; }
@keyframes modalFadeIn { from { opacity:0; transform:scale(0.95) translateY(10px); } to { opacity:1; transform:scale(1) translateY(0); } }
.de-modal-header { display:flex; justify-content:space-between; align-items:center; padding:10px 14px; background:#374151; color:#fff; flex-shrink:0; }
.de-modal-header h5 { margin:0; font-size:0.8rem; font-weight:800; text-transform:uppercase; letter-spacing:0.4px; }
.de-modal-close { background:none; border:none; color:#fff; font-size:1.4rem; cursor:pointer; line-height:1; padding:0 4px; }
.de-modal-body { flex:1; overflow:auto; padding:14px; }
.de-modal-footer { padding:10px 14px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:8px; flex-shrink:0; }
.form-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px; }
.form-grid .form-group { margin-bottom:0; }
.form-label { font-size:0.65rem; font-weight:700; color:#374151; margin-bottom:2px; display:block; text-transform:uppercase; letter-spacing:0.2px; }
.form-control-sm { font-size:0.72rem; padding:0.25rem 0.4rem; border:1px solid #d1d5db; border-radius:4px; width:100%; box-sizing:border-box; }
.form-control-sm:focus { outline:none; border-color:#059669; box-shadow:0 0 0 2px rgba(5,150,105,0.1); }
select.form-control-sm { cursor:pointer; }
@media (max-width:768px){.form-grid{grid-template-columns:1fr 1fr;}}
@media (max-width:480px){.form-grid{grid-template-columns:1fr;}}
</style>

<select id="yearFilter" class="d-none"><?php foreach (($availableYears ?? []) as $year): ?>
<option value="<?= esc($year) ?>" <?= ((int) $selectedYear === (int) $year) ? 'selected' : '' ?>><?= esc($year) ?></option><?php endforeach; ?></select>
<select id="monthFilter" class="d-none"><?php $months = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec']; ?>
<?php foreach ($months as $num => $label): ?><option value="<?= $num ?>" <?= ((int) $selectedMonth === (int) $num) ? 'selected' : '' ?>><?= esc($label) ?></option><?php endforeach; ?></select>

<div id="dashboardMain" style="display:flex;flex-direction:column;flex:1;min-height:0;">

<div id="primeBendedDash" class="dashboard-container">
<div class="kpi-row">
    <div class="kpi-card"><div class="kpi-label">Top Region</div><div class="kpi-value" id="seTopRegion">-</div></div>
    <div class="kpi-card"><div class="kpi-label">Highest Attainment</div><div class="kpi-value" id="seHighestVolume">0</div></div>
    <div class="kpi-card"><div class="kpi-label">Highest Revenue</div><div class="kpi-value" id="seHighestRevenue">₱0.00</div></div>
    <div class="kpi-card"><div class="kpi-label">Monthly Revenue</div><div class="kpi-value" id="seMonthlySales">₱0.00</div></div>
</div>
<div id="seDataView" class="view-section active">
    <div class="data-tables-grid">
        <div class="panel-card"><div class="table-header-section"><div class="table-title">SOUTH LUZON</div><div style="display:flex;gap:4px;"><select class="table-category-filter" id="seSouthCat" onchange="syncSeChartFilter('south');renderPrimeBendedTable('south')"><option value="attainment">HIGHEST % ATTAINMENT OVER BUDGET</option><option value="margin">HIGHEST % CONTRIBUTION MARGIN</option></select><button class="data-table-add-btn" onclick="openDashModal('se',null,'SOUTH LUZON')">+ Add</button></div></div>
            <div class="panel-body"><table class="analytics-table" id="seSouthTable"><thead id="seSouthHead"><tr><th>RANK</th><th>% ATTAINMENT</th><th>ACTUAL</th><th>BUDGET</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr></thead><tbody id="seSouthBody"></tbody></table></div></div>
        <div class="panel-card"><div class="table-header-section"><div class="table-title">NORTH & CENTRAL LUZON</div><div style="display:flex;gap:4px;"><select class="table-category-filter" id="seNcCat" onchange="syncSeChartFilter('nc');renderPrimeBendedTable('nc')"><option value="attainment">HIGHEST % ATTAINMENT OVER BUDGET</option><option value="margin">HIGHEST % CONTRIBUTION MARGIN</option></select><button class="data-table-add-btn" onclick="openDashModal('se',null,'NORTH & CENTRAL LUZON')">+ Add</button></div></div>
            <div class="panel-body"><table class="analytics-table" id="seNcTable"><thead id="seNcHead"><tr><th>RANK</th><th>% ATTAINMENT</th><th>ACTUAL</th><th>BUDGET</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr></thead><tbody id="seNcBody"></tbody></table></div></div>
        <div class="panel-card"><div class="table-header-section"><div class="table-title">VISAYAS</div><div style="display:flex;gap:4px;"><select class="table-category-filter" id="seVisCat" onchange="syncSeChartFilter('vis');renderPrimeBendedTable('vis')"><option value="attainment">HIGHEST % ATTAINMENT OVER BUDGET</option><option value="margin">HIGHEST % CONTRIBUTION MARGIN</option></select><button class="data-table-add-btn" onclick="openDashModal('se',null,'VISAYAS')">+ Add</button></div></div>
            <div class="panel-body"><table class="analytics-table" id="seVisTable"><thead id="seVisHead"><tr><th>RANK</th><th>% ATTAINMENT</th><th>ACTUAL</th><th>BUDGET</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr></thead><tbody id="seVisBody"></tbody></table></div></div>
        <div class="panel-card"><div class="table-header-section"><div class="table-title">MINDANAO</div><div style="display:flex;gap:4px;"><select class="table-category-filter" id="seMinCat" onchange="syncSeChartFilter('min');renderPrimeBendedTable('min')"><option value="attainment">HIGHEST % ATTAINMENT OVER BUDGET</option><option value="margin">HIGHEST % CONTRIBUTION MARGIN</option></select><button class="data-table-add-btn" onclick="openDashModal('se',null,'MINDANAO')">+ Add</button></div></div>
            <div class="panel-body"><table class="analytics-table" id="seMinTable"><thead id="seMinHead"><tr><th>RANK</th><th>% ATTAINMENT</th><th>ACTUAL</th><th>BUDGET</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr></thead><tbody id="seMinBody"></tbody></table></div></div>
    </div></div>
    <div id="seChartsView" class="view-section charts-section">
        <div class="charts-grid">
            <div class="panel-card"><div class="table-header-section" style="padding:0.4rem 0.6rem;display:flex;justify-content:space-between;align-items:center;"><div class="table-title" style="font-size:0.7rem;">SOUTH LUZON</div><select class="table-category-filter" id="seSouthChartCat" onchange="renderPrimeBendedCharts()" style="font-size:0.5rem !important;"><option value="all">ALL</option><option value="attainment">HIGHEST % ATTAINMENT OVER BUDGET</option><option value="margin">HIGHEST % CONTRIBUTION MARGIN</option></select></div><div class="row g-0" id="seSouthChartRow"><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST % ATTAINMENT OVER BUDGET</div><div class="apex-chart-wrap"><div id="seSouthAttChart"></div></div></div></div><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST % CONTRIBUTION MARGIN</div><div class="apex-chart-wrap"><div id="seSouthRevChart"></div></div></div></div></div></div>
            <div class="panel-card"><div class="table-header-section" style="padding:0.4rem 0.6rem;display:flex;justify-content:space-between;align-items:center;"><div class="table-title" style="font-size:0.7rem;">NORTH & CENTRAL LUZON</div><select class="table-category-filter" id="seNcChartCat" onchange="renderPrimeBendedCharts()" style="font-size:0.5rem !important;"><option value="all">ALL</option><option value="attainment">HIGHEST % ATTAINMENT OVER BUDGET</option><option value="margin">HIGHEST % CONTRIBUTION MARGIN</option></select></div><div class="row g-0" id="seNcChartRow"><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST % ATTAINMENT OVER BUDGET</div><div class="apex-chart-wrap"><div id="seNcAttChart"></div></div></div></div><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST % CONTRIBUTION MARGIN</div><div class="apex-chart-wrap"><div id="seNcRevChart"></div></div></div></div></div></div>
            <div class="panel-card"><div class="table-header-section" style="padding:0.4rem 0.6rem;display:flex;justify-content:space-between;align-items:center;"><div class="table-title" style="font-size:0.7rem;">VISAYAS</div><select class="table-category-filter" id="seVisChartCat" onchange="renderPrimeBendedCharts()" style="font-size:0.5rem !important;"><option value="all">ALL</option><option value="attainment">HIGHEST % ATTAINMENT OVER BUDGET</option><option value="margin">HIGHEST % CONTRIBUTION MARGIN</option></select></div><div class="row g-0" id="seVisChartRow"><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST % ATTAINMENT OVER BUDGET</div><div class="apex-chart-wrap"><div id="seVisAttChart"></div></div></div></div><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST % CONTRIBUTION MARGIN</div><div class="apex-chart-wrap"><div id="seVisRevChart"></div></div></div></div></div></div>
            <div class="panel-card"><div class="table-header-section" style="padding:0.4rem 0.6rem;display:flex;justify-content:space-between;align-items:center;"><div class="table-title" style="font-size:0.7rem;">MINDANAO</div><select class="table-category-filter" id="seMinChartCat" onchange="renderPrimeBendedCharts()" style="font-size:0.5rem !important;"><option value="all">ALL</option><option value="attainment">HIGHEST % ATTAINMENT OVER BUDGET</option><option value="margin">HIGHEST % CONTRIBUTION MARGIN</option></select></div><div class="row g-0" id="seMinChartRow"><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST % ATTAINMENT OVER BUDGET</div><div class="apex-chart-wrap"><div id="seMinAttChart"></div></div></div></div><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST % CONTRIBUTION MARGIN</div><div class="apex-chart-wrap"><div id="seMinRevChart"></div></div></div></div></div></div>
        </div>
    </div>

</div>

<div id="primeSpandrelDash" class="dashboard-container">
<div class="kpi-row">
    <div class="kpi-card"><div class="kpi-label">TOP REGION</div><div class="kpi-value" id="tbTopRegion">-</div></div>
    <div class="kpi-card"><div class="kpi-label">HIGHEST ATTAINMENT</div><div class="kpi-value" id="tbHighestVolume">0</div></div>
    <div class="kpi-card"><div class="kpi-label">HIGHEST GROWTH</div><div class="kpi-value" id="tbHighestRevenue">₱0.00</div></div>
    <div class="kpi-card"><div class="kpi-label">AVS GROWTH</div><div class="kpi-value" id="tbMonthlySales">₱0.00</div></div></div>
<div id="tbDataView" class="view-section active">
    <div class="data-tables-grid">
        <div class="panel-card"><div class="table-header-section"><div class="table-title">SOUTH LUZON</div><div style="display:flex;gap:4px;"><select class="table-category-filter" id="tbSouthCat" onchange="syncTbChartFilter('south');renderPrimeSpandrelTable('south')"><option value="margin">HIGHEST % GROWTH VS LM</option><option value="attainment">HIGHEST % OVERALL ATTAINMENT VS BUDGET</option></select><button class="data-table-add-btn" onclick="openDashModal('tb',null,'SOUTH LUZON')">+ Add</button></div></div>
            <div class="panel-body"><table class="analytics-table" id="tbSouthTable"><thead id="tbSouthHead"><tr><th>SALES OFFICE</th><th>% ATTAINMENT</th><th>ACTUAL</th><th>BUDGET</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr></thead><tbody id="tbSouthBody"></tbody></table></div></div>
        <div class="panel-card"><div class="table-header-section"><div class="table-title">NORTH & CENTRAL LUZON</div><div style="display:flex;gap:4px;"><select class="table-category-filter" id="tbNcCat" onchange="syncTbChartFilter('nc');renderPrimeSpandrelTable('nc')"><option value="margin">HIGHEST % GROWTH VS LM</option><option value="attainment">HIGHEST % OVERALL ATTAINMENT VS BUDGET</option></select><button class="data-table-add-btn" onclick="openDashModal('tb',null,'NORTH & CENTRAL LUZON')">+ Add</button></div></div>
            <div class="panel-body"><table class="analytics-table" id="tbNcTable"><thead id="tbNcHead"><tr><th>SALES OFFICE</th><th>% ATTAINMENT</th><th>ACTUAL</th><th>BUDGET</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr></thead><tbody id="tbNcBody"></tbody></table></div></div>
        <div class="panel-card"><div class="table-header-section"><div class="table-title">VISAYAS</div><div style="display:flex;gap:4px;"><select class="table-category-filter" id="tbVisCat" onchange="syncTbChartFilter('vis');renderPrimeSpandrelTable('vis')"><option value="margin">HIGHEST % GROWTH VS LM</option><option value="attainment">HIGHEST % OVERALL ATTAINMENT VS BUDGET</option></select><button class="data-table-add-btn" onclick="openDashModal('tb',null,'VISAYAS')">+ Add</button></div></div>
            <div class="panel-body"><table class="analytics-table" id="tbVisTable"><thead id="tbVisHead"><tr><th>SALES OFFICE</th><th>% ATTAINMENT</th><th>ACTUAL</th><th>BUDGET</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr></thead><tbody id="tbVisBody"></tbody></table></div></div>
        <div class="panel-card"><div class="table-header-section"><div class="table-title">MINDANAO</div><div style="display:flex;gap:4px;"><select class="table-category-filter" id="tbMinCat" onchange="syncTbChartFilter('min');renderPrimeSpandrelTable('min')"><option value="margin">HIGHEST % GROWTH VS LM</option><option value="attainment">HIGHEST % OVERALL ATTAINMENT VS BUDGET</option></select><button class="data-table-add-btn" onclick="openDashModal('tb',null,'MINDANAO')">+ Add</button></div></div>
            <div class="panel-body"><table class="analytics-table" id="tbMinTable"><thead id="tbMinHead"><tr><th>SALES OFFICE</th><th>% ATTAINMENT</th><th>ACTUAL</th><th>BUDGET</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr></thead><tbody id="tbMinBody"></tbody></table></div></div>
    </div></div>
<div id="tbChartsView" class="view-section charts-section">
    <div class="charts-grid">
        <div class="panel-card"><div class="table-header-section" style="padding:0.4rem 0.6rem;display:flex;justify-content:space-between;align-items:center;"><div class="table-title" style="font-size:0.7rem;">SOUTH LUZON</div><select class="table-category-filter" id="tbSouthChartCat" onchange="renderPrimeSpandrelCharts()" style="font-size:0.5rem !important;"><option value="all">ALL</option><option value="attainment">HIGHEST % OVERALL ATTAINMENT VS BUDGET</option><option value="growth">HIGHEST % GROWTH VS LM</option></select></div><div class="row g-0" id="tbSouthChartRow"><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST % ATTAINMENT OVER BUDGET</div><div class="apex-chart-wrap"><div id="tbSouthAttChart"></div></div></div></div><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST % GROWTH VS LM</div><div class="apex-chart-wrap"><div id="tbSouthGrowthChart"></div></div></div></div></div></div>
        <div class="panel-card"><div class="table-header-section" style="padding:0.4rem 0.6rem;display:flex;justify-content:space-between;align-items:center;"><div class="table-title" style="font-size:0.7rem;">NORTH & CENTRAL LUZON</div><select class="table-category-filter" id="tbNcChartCat" onchange="renderPrimeSpandrelCharts()" style="font-size:0.5rem !important;"><option value="all">ALL</option><option value="attainment">HIGHEST % OVERALL ATTAINMENT VS BUDGET</option><option value="growth">HIGHEST % GROWTH VS LM</option></select></div><div class="row g-0" id="tbNcChartRow"><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST % ATTAINMENT OVER BUDGET</div><div class="apex-chart-wrap"><div id="tbNcAttChart"></div></div></div></div><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST % GROWTH VS LM</div><div class="apex-chart-wrap"><div id="tbNcGrowthChart"></div></div></div></div></div></div>
        <div class="panel-card"><div class="table-header-section" style="padding:0.4rem 0.6rem;display:flex;justify-content:space-between;align-items:center;"><div class="table-title" style="font-size:0.7rem;">VISAYAS</div><select class="table-category-filter" id="tbVisChartCat" onchange="renderPrimeSpandrelCharts()" style="font-size:0.5rem !important;"><option value="all">ALL</option><option value="attainment">HIGHEST % OVERALL ATTAINMENT VS BUDGET</option><option value="growth">HIGHEST % GROWTH VS LM</option></select></div><div class="row g-0" id="tbVisChartRow"><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST % ATTAINMENT OVER BUDGET</div><div class="apex-chart-wrap"><div id="tbVisAttChart"></div></div></div></div><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST % GROWTH VS LM</div><div class="apex-chart-wrap"><div id="tbVisGrowthChart"></div></div></div></div></div></div>
        <div class="panel-card"><div class="table-header-section" style="padding:0.4rem 0.6rem;display:flex;justify-content:space-between;align-items:center;"><div class="table-title" style="font-size:0.7rem;">MINDANAO</div><select class="table-category-filter" id="tbMinChartCat" onchange="renderPrimeSpandrelCharts()" style="font-size:0.5rem !important;"><option value="all">ALL</option><option value="attainment">HIGHEST % OVERALL ATTAINMENT VS BUDGET</option><option value="growth">HIGHEST % GROWTH VS LM</option></select></div><div class="row g-0" id="tbMinChartRow"><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST % ATTAINMENT OVER BUDGET</div><div class="apex-chart-wrap"><div id="tbMinAttChart"></div></div></div></div><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST % GROWTH VS LM</div><div class="apex-chart-wrap"><div id="tbMinGrowthChart"></div></div></div></div></div></div>
    </div></div>

</div>

<div id="steelDeckDash" class="dashboard-container">
<div class="kpi-row">
    <div class="kpi-card"><div class="kpi-label">TOP VOLUME REGION</div><div class="kpi-value" id="ecTopRegion">-</div></div>
    <div class="kpi-card"><div class="kpi-label">TOP CM REGION</div><div class="kpi-value" id="ecHighestVolume">-</div></div>
    <div class="kpi-card"><div class="kpi-label">COMBINED VOLUME</div><div class="kpi-value" id="ecHighestRevenue">0</div></div>
    <div class="kpi-card"><div class="kpi-label">COMBINED CM</div><div class="kpi-value" id="ecMonthlySales">0.00</div></div>
</div>
<div id="ecDataView" class="view-section active">
    <div class="data-tables-grid">
        <div class="panel-card"><div class="table-header-section"><div class="table-title">SOUTH LUZON</div><div style="display:flex;gap:4px;"><select class="table-category-filter" id="ecSouthCat" onchange="syncEcChartFilter('south');renderSteelDeckTable('south')"><option value="volume">HIGHEST VOLUME CONTRIBUTOR PER REGION</option><option value="margin">HIGHEST CONTRIBUTION MARGIN PER REGION</option></select><button class="data-table-add-btn" onclick="openDashModal('ec',null,'SOUTH LUZON')">+ Add</button></div></div>
            <div class="panel-body"><table class="analytics-table" id="ecSouthTable"><thead id="ecSouthHead"><tr><th>RANK</th><th>ACTUAL VOLUME</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr></thead><tbody id="ecSouthBody"></tbody></table></div></div>
        <div class="panel-card"><div class="table-header-section"><div class="table-title">NORTH & CENTRAL LUZON</div><div style="display:flex;gap:4px;"><select class="table-category-filter" id="ecNcCat" onchange="syncEcChartFilter('nc');renderSteelDeckTable('nc')"><option value="volume">HIGHEST VOLUME CONTRIBUTOR PER REGION</option><option value="margin">HIGHEST CONTRIBUTION MARGIN PER REGION</option></select><button class="data-table-add-btn" onclick="openDashModal('ec',null,'NORTH & CENTRAL LUZON')">+ Add</button></div></div>
            <div class="panel-body"><table class="analytics-table" id="ecNcTable"><thead id="ecNcHead"><tr><th>RANK</th><th>ACTUAL VOLUME</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr></thead><tbody id="ecNcBody"></tbody></table></div></div>
        <div class="panel-card"><div class="table-header-section"><div class="table-title">VISAYAS</div><div style="display:flex;gap:4px;"><select class="table-category-filter" id="ecVisCat" onchange="syncEcChartFilter('vis');renderSteelDeckTable('vis')"><option value="volume">HIGHEST VOLUME CONTRIBUTOR PER REGION</option><option value="margin">HIGHEST CONTRIBUTION MARGIN PER REGION</option></select><button class="data-table-add-btn" onclick="openDashModal('ec',null,'VISAYAS')">+ Add</button></div></div>
            <div class="panel-body"><table class="analytics-table" id="ecVisTable"><thead id="ecVisHead"><tr><th>RANK</th><th>ACTUAL VOLUME</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr></thead><tbody id="ecVisBody"></tbody></table></div></div>
        <div class="panel-card"><div class="table-header-section"><div class="table-title">MINDANAO</div><div style="display:flex;gap:4px;"><select class="table-category-filter" id="ecMinCat" onchange="syncEcChartFilter('min');renderSteelDeckTable('min')"><option value="volume">HIGHEST VOLUME CONTRIBUTOR PER REGION</option><option value="margin">HIGHEST CONTRIBUTION MARGIN PER REGION</option></select><button class="data-table-add-btn" onclick="openDashModal('ec',null,'MINDANAO')">+ Add</button></div></div>
            <div class="panel-body"><table class="analytics-table" id="ecMinTable"><thead id="ecMinHead"><tr><th>RANK</th><th>ACTUAL VOLUME</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr></thead><tbody id="ecMinBody"></tbody></table></div></div>
    </div></div>
<div id="ecChartsView" class="view-section charts-section">
    <div class="charts-grid">
        <div class="panel-card"><div class="table-header-section" style="padding:0.4rem 0.6rem;display:flex;justify-content:space-between;align-items:center;"><div class="table-title" style="font-size:0.7rem;">SOUTH LUZON</div><select class="table-category-filter" id="ecSouthChartCat" onchange="renderSteelDeckCharts()" style="font-size:0.5rem !important;"><option value="all">ALL</option><option value="volume">HIGHEST VOLUME CONTRIBUTOR PER REGION</option><option value="margin">HIGHEST CONTRIBUTION MARGIN PER REGION</option></select></div><div class="row g-0" id="ecSouthChartRow"><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST VOLUME CONTRIBUTOR PER REGION</div><div class="apex-chart-wrap"><div id="ecSouthVolChart"></div></div></div></div><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST CONTRIBUTION MARGIN PER REGION</div><div class="apex-chart-wrap"><div id="ecSouthCmChart"></div></div></div></div></div></div>
        <div class="panel-card"><div class="table-header-section" style="padding:0.4rem 0.6rem;display:flex;justify-content:space-between;align-items:center;"><div class="table-title" style="font-size:0.7rem;">NORTH & CENTRAL LUZON</div><select class="table-category-filter" id="ecNcChartCat" onchange="renderSteelDeckCharts()" style="font-size:0.5rem !important;"><option value="all">ALL</option><option value="volume">HIGHEST VOLUME CONTRIBUTOR PER REGION</option><option value="margin">HIGHEST CONTRIBUTION MARGIN PER REGION</option></select></div><div class="row g-0" id="ecNcChartRow"><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST VOLUME CONTRIBUTOR PER REGION</div><div class="apex-chart-wrap"><div id="ecNcVolChart"></div></div></div></div><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST CONTRIBUTION MARGIN PER REGION</div><div class="apex-chart-wrap"><div id="ecNcCmChart"></div></div></div></div></div></div>
        <div class="panel-card"><div class="table-header-section" style="padding:0.4rem 0.6rem;display:flex;justify-content:space-between;align-items:center;"><div class="table-title" style="font-size:0.7rem;">VISAYAS</div><select class="table-category-filter" id="ecVisChartCat" onchange="renderSteelDeckCharts()" style="font-size:0.5rem !important;"><option value="all">ALL</option><option value="volume">HIGHEST VOLUME CONTRIBUTOR PER REGION</option><option value="margin">HIGHEST CONTRIBUTION MARGIN PER REGION</option></select></div><div class="row g-0" id="ecVisChartRow"><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST VOLUME CONTRIBUTOR PER REGION</div><div class="apex-chart-wrap"><div id="ecVisVolChart"></div></div></div></div><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST CONTRIBUTION MARGIN PER REGION</div><div class="apex-chart-wrap"><div id="ecVisCmChart"></div></div></div></div></div></div>
        <div class="panel-card"><div class="table-header-section" style="padding:0.4rem 0.6rem;display:flex;justify-content:space-between;align-items:center;"><div class="table-title" style="font-size:0.7rem;">MINDANAO</div><select class="table-category-filter" id="ecMinChartCat" onchange="renderSteelDeckCharts()" style="font-size:0.5rem !important;"><option value="all">ALL</option><option value="volume">HIGHEST VOLUME CONTRIBUTOR PER REGION</option><option value="margin">HIGHEST CONTRIBUTION MARGIN PER REGION</option></select></div><div class="row g-0" id="ecMinChartRow"><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST VOLUME CONTRIBUTOR PER REGION</div><div class="apex-chart-wrap"><div id="ecMinVolChart"></div></div></div></div><div class="chart-half"><div class="panel-body"><div class="chart-label">HIGHEST CONTRIBUTION MARGIN PER REGION</div><div class="apex-chart-wrap"><div id="ecMinCmChart"></div></div></div></div></div></div>
    </div></div>

</div>

<div id="masterDataDash" class="dashboard-container">
    <!-- MASTER DATA SUMMARY HEADERS -->
    <div class="md-section" style="background:#fff;border-radius:12px;border:1px solid #eef0f4;padding:0.75rem;margin-bottom:8px;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <!-- KPI SUMMARY ROW -->
        <div class="kpi-row" style="margin-bottom:12px;">
            <div class="kpi-card"><div class="kpi-label">Top Region</div><div class="kpi-value" id="mdTopRegionName2">-</div></div>
            <div class="kpi-card"><div class="kpi-label">Highest Volume</div><div class="kpi-value" id="mdHighestVolume">0</div></div>
            <div class="kpi-card"><div class="kpi-label">Highest Revenue</div><div class="kpi-value" id="mdHighestRevenue">₱0.00</div></div>
            <div class="kpi-card"><div class="kpi-label">TOTAL PARTICIPANTS</div><div class="kpi-value" id="mdTotalSold">0</div></div>
        </div>

        <!-- PER-REGION PER-PRODUCT GRID -->
        <div id="mdProductGrid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;"></div>
    </div>

        <!-- MASTER DATA PER-REGION SUMMARY TABLES -->
    <div id="mdRegionTablesContainer" style="display:grid;grid-template-columns:1fr 1fr;grid-template-rows:1fr 1fr;gap:8px;flex:1;min-height:0;">
        <div class="panel-card elite-table-card" style="border-radius:12px;border:1px solid #eef0f4;display:flex;flex-direction:column;overflow:hidden;">
            <div class="table-header-section"><div class="table-title">SOUTH LUZON</div></div>
            <div style="flex:1;overflow:auto;padding:0;">
                <table class="analytics-table" id="mdSouthTable" style="width:100%;table-layout:fixed;border-collapse:collapse;">
                    <thead><tr><th style="width:22%;text-align:left;padding-left:0.5rem;">NAME</th><th style="width:20%;">PB REVENUE</th><th style="width:20%;">PS REVENUE</th><th style="width:20%;">SD REVENUE</th><th style="width:18%;">TOTAL</th></tr></thead>
                    <tbody id="mdSouthBody"></tbody>
                </table>
            </div>
        </div>
        <div class="panel-card elite-table-card" style="border-radius:12px;border:1px solid #eef0f4;display:flex;flex-direction:column;overflow:hidden;">
            <div class="table-header-section"><div class="table-title">NORTH & CENTRAL LUZON</div></div>
            <div style="flex:1;overflow:auto;padding:0;">
                <table class="analytics-table" id="mdNcTable" style="width:100%;table-layout:fixed;border-collapse:collapse;">
                    <thead><tr><th style="width:22%;text-align:left;padding-left:0.5rem;">NAME</th><th style="width:20%;">PB REVENUE</th><th style="width:20%;">PS REVENUE</th><th style="width:20%;">SD REVENUE</th><th style="width:18%;">TOTAL</th></tr></thead>
                    <tbody id="mdNcBody"></tbody>
                </table>
            </div>
        </div>
        <div class="panel-card elite-table-card" style="border-radius:12px;border:1px solid #eef0f4;display:flex;flex-direction:column;overflow:hidden;">
            <div class="table-header-section"><div class="table-title">VISAYAS</div></div>
            <div style="flex:1;overflow:auto;padding:0;">
                <table class="analytics-table" id="mdVisTable" style="width:100%;table-layout:fixed;border-collapse:collapse;">
                    <thead><tr><th style="width:22%;text-align:left;padding-left:0.5rem;">NAME</th><th style="width:20%;">PB REVENUE</th><th style="width:20%;">PS REVENUE</th><th style="width:20%;">SD REVENUE</th><th style="width:18%;">TOTAL</th></tr></thead>
                    <tbody id="mdVisBody"></tbody>
                </table>
            </div>
        </div>
        <div class="panel-card elite-table-card" style="border-radius:12px;border:1px solid #eef0f4;display:flex;flex-direction:column;overflow:hidden;">
            <div class="table-header-section"><div class="table-title">MINDANAO</div></div>
            <div style="flex:1;overflow:auto;padding:0;">
                <table class="analytics-table" id="mdMinTable" style="width:100%;table-layout:fixed;border-collapse:collapse;">
                    <thead><tr><th style="width:22%;text-align:left;padding-left:0.5rem;">NAME</th><th style="width:20%;">PB REVENUE</th><th style="width:20%;">PS REVENUE</th><th style="width:20%;">SD REVENUE</th><th style="width:18%;">TOTAL</th></tr></thead>
                    <tbody id="mdMinBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>

<div class="dash-action-bar" style="display:flex;justify-content:space-between;gap:8px;padding:0.5rem 0;flex-shrink:0;align-items:center;flex-wrap:wrap;">
    <div style="display:flex;gap:6px;align-items:center;">
        <button class="de-btn de-btn-add" id="dashInsertNewBtn">➕ Insert New Data</button>
        <button class="de-btn de-btn-primary" id="dashRetrieveBtn">📂 Retrieve Data</button>
    </div>
    <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
        <span style="font-size:0.65rem;font-weight:700;color:#475569;text-transform:uppercase;">Month:</span>
        <input type="number" id="dashMonthInput" min="1" max="12" value="4" style="width:60px;font-size:0.72rem;padding:0.25rem 0.4rem;border:1.5px solid #d1d5db;border-radius:6px;">
        <span style="font-size:0.65rem;font-weight:700;color:#475569;text-transform:uppercase;margin-left:4px;">Year:</span>
        <input type="number" id="dashYearInput" min="2020" max="2099" value="2026" style="width:70px;font-size:0.72rem;padding:0.25rem 0.4rem;border:1.5px solid #d1d5db;border-radius:6px;">
        <button class="de-btn de-btn-export" id="dashExportBtn">📥 Export</button>
        <button class="de-btn de-btn-primary" id="dashSaveBtn">💾 Save</button>
    </div>
</div>

<div class="de-modal-overlay" id="dashModal">
    <div class="de-modal">
        <div class="de-modal-header">
            <h5 id="dashModalTitle">Add Record</h5>
            <button class="de-modal-close" onclick="closeDashModal()">&times;</button>
        </div>
        <div class="de-modal-body" id="dashModalBody"></div>
        <div class="de-modal-footer">
            <button class="de-btn de-btn-export" onclick="closeDashModal()">Cancel</button>
            <button class="de-btn de-btn-primary" id="dashModalSaveBtn" onclick="saveDashRecord()">Save</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.2"></script>
<script>
(function() {
    var yearEl = document.getElementById('yearFilter');
    var monthEl = document.getElementById('monthFilter');
    var sideRegion = document.getElementById('sideRegionFilter');
    var dashYear = document.getElementById('dashboardYearFilter');
    var dashMonth = document.getElementById('dashboardMonthFilter');

    function fmt(v) { return '₱' + Number(v||0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}); }
    function setText(id, v) { var el = document.getElementById(id); if(el) el.textContent = v; }
    var regionNames = {south:'SOUTH LUZON',nc:'NORTH & CENTRAL LUZON',vis:'VISAYAS',min:'MINDANAO'};
    var primeBendedData = {}, primeSpandrelData = {}, steelDeckData = {};
    var currentTab = 'primeBended';
    var chartInstances = {};
    window.chartInstances = chartInstances;

    var dashModalType = 'se';
    var dashModalAction = 'create';
    var dashModalEditId = null;
    var dashModalCategory = 'attainment';
    var dashRegions = ['SOUTH LUZON', 'NORTH & CENTRAL LUZON', 'VISAYAS', 'MINDANAO'];

    function organizeByRegion(data) {
        if (!data || !Array.isArray(data)) return {south:[],nc:[],vis:[],min:[]};
        var map = {'SOUTH LUZON':'south','SOUTH':'south','NORTH & CENTRAL LUZON':'nc','NORTH & CENTRAL':'nc','NC':'nc','VISAYAS':'vis','VIS':'vis','MINDANAO':'min','MIN':'min'};
        var o = {south:[],nc:[],vis:[],min:[]};
        data.forEach(function(r){ var k = map[(r.region||'').toUpperCase()]||'south'; o[k].push(r); });
        return o;
    }

    function generatePrimeBendedData() {
        if (!window.dashboardData || !window.hasDashboardData) return {south:[],nc:[],vis:[],min:[]};
        var o = organizeByRegion(window.dashboardData.sales_excellence);
        var r = {};
        ['south','nc','vis','min'].forEach(function(reg) {
            var raw = o[reg]||[];
            var items = [];
            raw.forEach(function(rr){
                // Determine category based on which fields have values
                var cat = (rr.category || '').toLowerCase();
                // If category field is empty or invalid, detect from actual field values
                if (cat !== 'attainment' && cat !== 'margin') {
                    // Check if attainment fields are populated vs margin fields
                    if (parseFloat(rr.attainment_percent) > 0 || parseFloat(rr.budget) > 0) {
                        cat = 'attainment';
                    } else if (parseFloat(rr.margin) > 0 || parseFloat(rr.revenue) > 0) {
                        cat = 'margin';
                    } else {
                        cat = 'attainment'; // default
                    }
                }
                items.push({
                    uuid:rr.uuid||'', id:parseInt(rr.id)||0,
                    company:rr.area||'N/A', n:rr.name||'N/A', a:rr.area||'N/A', pos:rr.position||'N/A',
                    revenue:parseFloat(rr.revenue)||0, qtyInvoice:parseFloat(rr.actual_volume)||0,
                    grossAmount:parseFloat(rr.actual_cm)||0, volume:parseFloat(rr.budget)||0,
                    actual:parseFloat(rr.actual_volume)||0, budget:parseFloat(rr.budget)||0,
                    pricelf:parseFloat(rr.price_lf)||0, cm:parseFloat(rr.actual_cm)||0,
                    att:parseFloat(rr.attainment_percent)||0, margin:parseFloat(rr.margin)||0,
                    category: cat
                });
            });
            r[reg] = items;
        });
        return r;
    }

    function generatePrimeSpandrelData() {
        if (!window.dashboardData || !window.hasDashboardData) return {south:[],nc:[],vis:[],min:[]};
        var o = organizeByRegion(window.dashboardData.top_branch);
        var r = {};
        ['south','nc','vis','min'].forEach(function(reg) {
            var items = o[reg]||[];
            var c = [];
            items.forEach(function(i){ c.push({uuid:i.uuid||'',id:parseInt(i.id)||0,office:i.sales_office||'N/A',growth:parseFloat(i.growth_percent)||0,att:parseFloat(i.attainment_percent)||0,n:i.name||'N/A',a:i.area||'N/A',pos:i.position||'N/A',revenue:parseFloat(i.revenue)||0,currentMonth:parseFloat(i.current_month)||0,lastMonth:parseFloat(i.last_month)||0,actual:parseFloat(i.actual)||0,budget:parseFloat(i.budget)||0,category:i.category||'attainment'}); });
            r[reg] = c;
        });
        return r;
    }

    function generateSteelDeckData() {
        var r = {south:[],nc:[],vis:[],min:[]};
        if (!window.dashboardData||!window.hasDashboardData) return r;
        var records = window.dashboardData.elite_circle_data || [];
        var map = {'SOUTH LUZON':'south','NORTH & CENTRAL LUZON':'nc','VISAYAS':'vis','MINDANAO':'min'};
        records.forEach(function(rec){
            var k = map[(rec.region||'').toUpperCase()];
            if (!k) return;
            r[k].push({uuid:rec.uuid||'',id:parseInt(rec.id)||0, company:rec.company||rec.area||'N/A', n:rec.name||'N/A', a:rec.area||'N/A', pos:rec.position||'N/A', revenue:parseFloat(rec.revenue)||0, qtyInvoice:parseFloat(rec.quantity_invoice)||0, grossAmount:parseFloat(rec.gross_amount)||0, volume:parseFloat(rec.volume)||0, category:rec.category||'volume'});
        });
        return r;
    }

    function getPrimeBendedData(){return generatePrimeBendedData();}
    function getPrimeSpandrelData(){return generatePrimeSpandrelData();}
    function getSteelDeckData(){return generateSteelDeckData();}

    function updateTopbarTitle(tab) {
        var titleEl = document.querySelector('.topbar-title');
        var subEl = document.querySelector('.topbar-sub');
        if (!titleEl) return;
        var titles = {
            masterData: { title: '<span style="color: var(--ugc-red);">OVERVIEW AND ANALYTICS</span> DASHBOARD', sub: 'Consolidated summary and analytics across all product segments' },
            primeBended: { title: 'SALES EXCELLENCE <span style="color: var(--ugc-red);">AWARDEE</span> DASHBOARD', sub: 'Recognizing Top Performing Dealers' },
            primeSpandrel: { title: '<span style="color: var(--ugc-red);">TOP BRANCH RECOGNITION</span> DASHBOARD', sub: 'Recognizing Top Performing Branch' },
            steelDeck: { title: 'SALES EXCELLENCE <span style="color: var(--ugc-red);">ELITE CIRCLE</span> DASHBOARD', sub: 'Top Volume & CM Per Region - Recognizing Top Performing Dealers' }
        };
        var t = titles[tab] || titles.masterData;
        titleEl.innerHTML = t.title;
        if (subEl) subEl.innerHTML = t.sub;
    }

    function switchTab(tab) {
        currentTab=tab;
        document.querySelectorAll('.dashboard-container').forEach(function(c){c.classList.remove('active');});
        document.querySelectorAll('.menu-item').forEach(function(m){m.classList.remove('active');});
        var ids={masterData:'masterDataDash',primeBended:'primeBendedDash',primeSpandrel:'primeSpandrelDash',steelDeck:'steelDeckDash'};
        var mids={masterData:'masterDataMenu',primeBended:'primeBendedMenu',primeSpandrel:'primeSpandrelMenu',steelDeck:'steelDeckMenu'};
        if(ids[tab])document.getElementById(ids[tab]).classList.add('active');
        if(mids[tab])document.getElementById(mids[tab]).classList.add('active');
        updateTopbarTitle(tab); updateViewToggle(); renderAll(chartsIdActive());
    }
    
    // ============================================================
    // MASTER DATA DASHBOARD
    // ============================================================
    
    var mdRegions = ['SOUTH LUZON', 'NORTH & CENTRAL LUZON', 'VISAYAS', 'MINDANAO'];
    
    function getMasterDataByRegion() {
        if (!window.dashboardData || !window.hasDashboardData) return {};
        var result = {};
        mdRegions.forEach(function(reg){
            var regKey = reg.toUpperCase();
            var pb = (window.dashboardData.sales_excellence||[]).filter(function(r){ return (r.region||'').toUpperCase() === regKey; });
            var ps = (window.dashboardData.top_branch||[]).filter(function(r){ return (r.region||'').toUpperCase() === regKey; });
            var sd = (window.dashboardData.elite_circle_data||[]).filter(function(r){ return (r.region||'').toUpperCase() === regKey; });
            
            var pbQty = pb.reduce(function(s,r){ return s + (parseFloat(r.actual_volume)||0); }, 0);
            var pbVol = pb.reduce(function(s,r){ return s + (parseFloat(r.budget)||0); }, 0);
            var pbRev = pb.reduce(function(s,r){ return s + (parseFloat(r.revenue)||0); }, 0);
            var psQty = ps.reduce(function(s,r){ return s + (parseFloat(r.current_month)||0); }, 0);
            var psVol = ps.reduce(function(s,r){ return s + (parseFloat(r.budget)||0); }, 0);
            var psRev = ps.reduce(function(s,r){ return s + (parseFloat(r.revenue)||0); }, 0);
            var sdQty = sd.reduce(function(s,r){ return s + (parseFloat(r.quantity_invoice)||0); }, 0);
            var sdVol = sd.reduce(function(s,r){ return s + (parseFloat(r.volume)||0); }, 0);
            var sdRev = sd.reduce(function(s,r){ return s + (parseFloat(r.revenue)||0); }, 0);
            
            result[reg] = {
                primeBended: { count: pb.length, qty: pbQty, volume: pbVol, revenue: pbRev, records: pb },
                primeSpandrel: { count: ps.length, qty: psQty, volume: psVol, revenue: psRev, records: ps },
                steelDeck: { count: sd.length, qty: sdQty, volume: sdVol, revenue: sdRev, records: sd },
                totalQty: pbQty + psQty + sdQty,
                totalVolume: pbVol + psVol + sdVol,
                totalRevenue: pbRev + psRev + sdRev
            };
        });
        return result;
    }
    
    function renderMasterData() {
        var mdData = getMasterDataByRegion();
        var pbTotalQty = 0, psTotalQty = 0, sdTotalQty = 0;
        var pbTotalVol = 0, psTotalVol = 0, sdTotalVol = 0;
        var pbTotalRev = 0, psTotalRev = 0, sdTotalRev = 0;
        
        // Calculate global totals and find highest values
        var globalHighestVol = {value: -Infinity, region: null};
        var globalHighestRev = {value: -Infinity, region: null};
        
        // Track unique participants across all regions and segments
        var uniqueParticipantNames = {};
        var pbParticipantNames = {};
        var psParticipantNames = {};
        var sdParticipantNames = {};
        
        mdRegions.forEach(function(reg) {
            var d = mdData[reg];
            if (!d) return;
            pbTotalQty += d.primeBended.qty; psTotalQty += d.primeSpandrel.qty; sdTotalQty += d.steelDeck.qty;
            pbTotalVol += d.primeBended.volume; psTotalVol += d.primeSpandrel.volume; sdTotalVol += d.steelDeck.volume;
            pbTotalRev += d.primeBended.revenue; psTotalRev += d.primeSpandrel.revenue; sdTotalRev += d.steelDeck.revenue;
            
            // Count unique participants by name
            (d.primeBended.records||[]).forEach(function(r) {
                var n = (r.name||'').trim();
                if (n && n !== 'N/A' && n !== '') { uniqueParticipantNames[n] = true; pbParticipantNames[n] = true; }
            });
            (d.primeSpandrel.records||[]).forEach(function(r) {
                var n = (r.name||'').trim();
                if (n && n !== 'N/A' && n !== '') { uniqueParticipantNames[n] = true; psParticipantNames[n] = true; }
            });
            (d.steelDeck.records||[]).forEach(function(r) {
                var n = (r.name||'').trim();
                if (n && n !== 'N/A' && n !== '') { uniqueParticipantNames[n] = true; sdParticipantNames[n] = true; }
            });
            
            if (d.totalVolume > globalHighestVol.value) { globalHighestVol.value = d.totalVolume; globalHighestVol.region = reg; }
            if (d.totalRevenue > globalHighestRev.value) { globalHighestRev.value = d.totalRevenue; globalHighestRev.region = reg; }
        });
        
        var totalUniqueParticipants = Object.keys(uniqueParticipantNames).length;
        var pbUniqueCount = Object.keys(pbParticipantNames).length;
        var psUniqueCount = Object.keys(psParticipantNames).length;
        var sdUniqueCount = Object.keys(sdParticipantNames).length;
        
        // Rank regions to determine Top Region (ONLY if there is actual data)
        var topRegion = '-';
        if (totalUniqueParticipants > 0) {
            var regionScores = {};
            mdRegions.forEach(function(reg) { regionScores[reg] = {score: 0}; });
            function assignRanks(arr, metric) {
                var sorted = arr.slice().sort(function(a,b){ return (b[metric]||0) - (a[metric]||0); });
                sorted.forEach(function(item, idx) { regionScores[item.reg].score += (4 - idx); });
            }
            var metrics = mdRegions.map(function(reg){ return {reg:reg, vol:(mdData[reg]||{totalVolume:0}).totalVolume, rev:(mdData[reg]||{totalRevenue:0}).totalRevenue, sold:(mdData[reg]||{totalQty:0}).totalQty}; });
            assignRanks(metrics, 'vol'); assignRanks(metrics, 'rev'); assignRanks(metrics, 'sold');
            var topScore = -1;
            mdRegions.forEach(function(reg) { if (regionScores[reg].score > topScore) { topScore = regionScores[reg].score; topRegion = reg; } });
        }
        
        // Update KPI summary cards
        setText('mdTopRegionName2', topRegion);
        setText('mdHighestVolume', globalHighestVol.value > -Infinity ? Number(globalHighestVol.value).toLocaleString() : '0');
        setText('mdHighestRevenue', globalHighestRev.value > -Infinity ? fmt(globalHighestRev.value) : '₱0.00');
        // TOTAL PARTICIPANTS with per-product breakdown
        var totalSoldLabel = '';
        if (totalUniqueParticipants > 0) {
            totalSoldLabel = Number(totalUniqueParticipants).toLocaleString() + ' (PRIME BENDED: ' + Number(pbUniqueCount).toLocaleString() + ', PRIME SPANDREL: ' + Number(psUniqueCount).toLocaleString() + ', STEEL DECK: ' + Number(sdUniqueCount).toLocaleString() + ')';
        } else {
            totalSoldLabel = '0';
        }
        setText('mdTotalSold', totalSoldLabel);
        
        // Render per-region per-product grid
        var grid = document.getElementById('mdProductGrid');
        grid.innerHTML = '';
        var productLabels = ['PRIME BENDED', 'PRIME SPANDREL', 'STEEL DECK'];
        var productColors = ['#059669', '#dc2626', '#6b7280'];
        
        mdRegions.forEach(function(reg) {
            var d = mdData[reg] || {primeBended:{qty:0,volume:0,revenue:0}, primeSpandrel:{qty:0,volume:0,revenue:0}, steelDeck:{qty:0,volume:0,revenue:0}};
            var products = [d.primeBended, d.primeSpandrel, d.steelDeck];
            
            var col = document.createElement('div');
            col.style.cssText = 'background:#fff;border-radius:8px;border:1px solid #e2e8f0;overflow:hidden;display:flex;flex-direction:column;';
            
            // Region header
            var header = document.createElement('div');
            header.style.cssText = 'background:#6b7280;padding:0.35rem 0.5rem;text-align:center;font-size:0.65rem;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:0.3px;';
            header.textContent = reg;
            col.appendChild(header);
            
            // Product rows
            products.forEach(function(p, pi) {
                var row = document.createElement('div');
                row.style.cssText = 'display:flex;flex-direction:column;padding:0.25rem 0.4rem;border-bottom:1px solid #f1f5f9;';
                if (pi === products.length - 1) row.style.borderBottom = 'none';
                row.innerHTML = '<div style="font-size:0.55rem;font-weight:700;color:'+productColors[pi]+';text-transform:uppercase;letter-spacing:0.2px;">'+productLabels[pi]+'</div>'+
                    '<div style="display:flex;justify-content:space-between;font-size:0.6rem;color:#475569;margin-top:1px;">'+
                    '<span>Qty: <strong style="color:#1e293b;">'+Number(p.qty).toLocaleString()+'</strong></span>'+
                    '<span>Vol: <strong style="color:#1e293b;">'+Number(p.volume).toLocaleString()+'</strong></span>'+
                    '<span>Rev: <strong style="color:#1e293b;">'+fmt(p.revenue)+'</strong></span>'+
                    '</div>';
                col.appendChild(row);
            });
            
            // Region total row
            var totalRow = document.createElement('div');
            totalRow.style.cssText = 'background:#f8fafc;padding:0.25rem 0.4rem;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;font-size:0.6rem;font-weight:700;color:#374151;';
            totalRow.innerHTML = '<span>TOTAL</span><span>'+Number(d.totalQty).toLocaleString()+' | '+Number(d.totalVolume).toLocaleString()+' | '+fmt(d.totalRevenue)+'</span>';
            col.appendChild(totalRow);
            
            grid.appendChild(col);
        });
        
        
        renderMasterDataRegionTables(mdData);
    }
    
    function renderMasterDataRegionTables(mdData) {
        var regionMap = {
            south: { id: 'mdSouthBody', regKey: 'SOUTH LUZON' },
            nc: { id: 'mdNcBody', regKey: 'NORTH & CENTRAL LUZON' },
            vis: { id: 'mdVisBody', regKey: 'VISAYAS' },
            min: { id: 'mdMinBody', regKey: 'MINDANAO' }
        };
        var regKeys = ['south', 'nc', 'vis', 'min'];
        
        regKeys.forEach(function(rk) {
            var info = regionMap[rk];
            var regKey = info.regKey;
            var tbody = document.getElementById(info.id);
            if (!tbody) return;
            tbody.innerHTML = '';
            
            var d = mdData[regKey];
            if (!d) {
                tbody.innerHTML = '<tr><td colspan="5" style="padding:1.5rem;text-align:center;color:#94a3b8;">No data for this region.</td></tr>';
                return;
            }
            
            var pbRecords = d.primeBended.records || [];
            var psRecords = d.primeSpandrel.records || [];
            var sdRecords = d.steelDeck.records || [];
            
            // Build per-name summary per region
            var regionSummary = {};
            
            pbRecords.forEach(function(r) {
                var name = (r.name || '').trim();
                if (!name || name === 'N/A' || name === '') return;
                if (!regionSummary[name]) regionSummary[name] = { pbRev: 0, psRev: 0, sdRev: 0 };
                regionSummary[name].pbRev += parseFloat(r.revenue) || 0;
            });
            
            psRecords.forEach(function(r) {
                var name = (r.name || '').trim();
                if (!name || name === 'N/A' || name === '') return;
                if (!regionSummary[name]) regionSummary[name] = { pbRev: 0, psRev: 0, sdRev: 0 };
                regionSummary[name].psRev += parseFloat(r.revenue) || 0;
            });
            
            sdRecords.forEach(function(r) {
                var name = (r.name || '').trim();
                if (!name || name === 'N/A' || name === '') return;
                if (!regionSummary[name]) regionSummary[name] = { pbRev: 0, psRev: 0, sdRev: 0 };
                regionSummary[name].sdRev += parseFloat(r.revenue) || 0;
            });
            
            var regionArray = Object.keys(regionSummary).map(function(name) {
                return {
                    name: name,
                    pbRev: regionSummary[name].pbRev,
                    psRev: regionSummary[name].psRev,
                    sdRev: regionSummary[name].sdRev,
                    totalRev: regionSummary[name].pbRev + regionSummary[name].psRev + regionSummary[name].sdRev
                };
            });
            regionArray.sort(function(a, b) { return b.totalRev - a.totalRev; });
            
            if (!regionArray.length) {
                tbody.innerHTML = '<tr><td colspan="5" style="padding:1.5rem;text-align:center;color:#94a3b8;">No data for this region.</td></tr>';
                return;
            }
            
            regionArray.forEach(function(item) {
                var tr = document.createElement('tr');
                tr.innerHTML = 
                    '<td style="text-align:left;padding-left:0.5rem;font-weight:600;color:#1e293b;">' + item.name + '</td>' +
                    '<td style="font-weight:600;color:#059669;">' + fmt(item.pbRev) + '</td>' +
                    '<td style="font-weight:600;color:#3b82f6;">' + fmt(item.psRev) + '</td>' +
                    '<td style="font-weight:600;color:#8b5cf6;">' + fmt(item.sdRev) + '</td>' +
                    '<td style="font-weight:700;color:#374151;background:#f8fafc;">' + fmt(item.totalRev) + '</td>';
                tbody.appendChild(tr);
            });
        });
    }
    
    // ============================================================
    // END MASTER DATA
    // ============================================================
    
    window.handleTabSwitch = switchTab;
    window.renderPrimeBendedTable = renderPrimeBendedTable;
    window.renderPrimeSpandrelTable = renderPrimeSpandrelTable;
    window.renderSteelDeckTable = renderSteelDeckTable;
    window.renderPrimeBendedCharts = renderPrimeBendedCharts;
    window.renderPrimeSpandrelCharts = renderPrimeSpandrelCharts;
    window.renderSteelDeckCharts = renderSteelDeckCharts;
    function chartsIdActive() {
        if(currentTab==='primeBended')return document.getElementById('seChartsView').classList.contains('active');
        if(currentTab==='primeSpandrel')return document.getElementById('tbChartsView').classList.contains('active');
        if(currentTab==='steelDeck')return document.getElementById('ecChartsView').classList.contains('active');
        return false;
    }

    function updateViewToggle() {
        var c=document.getElementById('viewToggleContainer'); if(!c)return;
        var insertBtn = document.getElementById('dashInsertNewBtn');
        if (currentTab === 'masterData') {
            c.innerHTML = '';
            c.style.display = 'none';
            if (insertBtn) insertBtn.style.display = 'none';
            return;
        }
        if (insertBtn) insertBtn.style.display = '';
        c.style.display='flex';
        var viewMap = {
            primeBended: { data: 'seDataView', charts: 'seChartsView' },
            primeSpandrel: { data: 'tbDataView', charts: 'tbChartsView' },
            steelDeck: { data: 'ecDataView', charts: 'ecChartsView' }
        };
        var views = viewMap[currentTab] || viewMap.primeBended;
        var viewList = [
            {id: views.data, label: 'DATA'},
            {id: views.charts, label: 'CHARTS'}
        ];
        c.innerHTML='';
        viewList.forEach(function(v){
            var btn=document.createElement('button');
            btn.className='view-btn'+(document.getElementById(v.id).classList.contains('active')?' active':'');
            btn.textContent=v.label;
            btn.onclick=function(){
                var map={primeBended:'primeBendedDash',primeSpandrel:'primeSpandrelDash',steelDeck:'steelDeckDash'};
                var contId=map[currentTab]||'primeBendedDash';
                document.getElementById(contId).querySelectorAll('.view-section').forEach(function(s){s.classList.remove('active');});
                document.getElementById(v.id).classList.add('active');
                c.querySelectorAll('.view-btn').forEach(function(b){b.classList.remove('active');});
                btn.classList.add('active');
                if(v.id.indexOf('ChartsView')!==-1)renderAll(true);
            };
            c.appendChild(btn);
        });
    }

    function renderAll(rc) {
        if(currentTab==='masterData'){renderMasterData();applyRegionFilter();}
        else if(currentTab==='primeBended'){renderPrimeBended();if(rc||document.getElementById('seChartsView').classList.contains('active'))renderPrimeBendedCharts();applyRegionFilter();}
        else if(currentTab==='primeSpandrel'){renderPrimeSpandrel();if(rc||document.getElementById('tbChartsView').classList.contains('active'))renderPrimeSpandrelCharts();applyRegionFilter();}
        else if(currentTab==='steelDeck'){renderSteelDeck();if(rc||document.getElementById('ecChartsView').classList.contains('active'))renderSteelDeckCharts();applyRegionFilter();}
    }

    function applyRegionFilter() {
        var region=sideRegion?sideRegion.value:'';
        var map={primeBended:'primeBendedDash',primeSpandrel:'primeSpandrelDash',steelDeck:'steelDeckDash'};
        
        // MASTER DATA FILTER
        if (currentTab === 'masterData') {
            var mdContainer = document.getElementById('masterDataDash');
            if (!mdContainer) return;
            
            // Filter per-region tables in mdRegionTablesContainer
            var tableContainer = document.getElementById('mdRegionTablesContainer');
            if (tableContainer) {
                tableContainer.querySelectorAll('.panel-card.elite-table-card').forEach(function(card){
                    var t = card.querySelector('.table-title');
                    if (!t) return;
                    card.style.display = (!region || t.textContent.trim() === region) ? '' : 'none';
                });
            }
            
            // Filter per-region product grid columns in mdProductGrid
            var productGrid = document.getElementById('mdProductGrid');
            if (productGrid) {
                productGrid.querySelectorAll('div').forEach(function(col){
                    // Each column has a region header div
                    var header = col.querySelector('div');
                    if (!header || !header.textContent.trim()) return;
                    // Check if this is a region column (has header with region name)
                    var mdRegionsList = ['SOUTH LUZON', 'NORTH & CENTRAL LUZON', 'VISAYAS', 'MINDANAO'];
                    var isRegionCol = mdRegionsList.some(function(r){ return header.textContent.trim() === r; });
                    if (!isRegionCol) return;
                    col.style.display = (!region || header.textContent.trim() === region) ? '' : 'none';
                });
            }
            
            // Toggle filtered class for responsive layout
            if (region) {
                // Make product grid single column and wider when filtered
                if (productGrid) {
                    productGrid.style.gridTemplateColumns = '1fr';
                }
                if (tableContainer) {
                    tableContainer.classList.add('filtered');
                    tableContainer.style.gridTemplateColumns = '1fr';
                    tableContainer.style.gridTemplateRows = '1fr';
                }
            } else {
                if (productGrid) {
                    productGrid.style.gridTemplateColumns = 'repeat(4,1fr)';
                }
                if (tableContainer) {
                    tableContainer.classList.remove('filtered');
                    tableContainer.style.gridTemplateColumns = '1fr 1fr';
                    tableContainer.style.gridTemplateRows = '1fr 1fr';
                }
            }
            return;
        }
        
        var cid=map[currentTab]||'primeBendedDash';
        var c=document.getElementById(cid); if(!c)return;
        c.querySelectorAll('.data-tables-grid .panel-card,.charts-grid .panel-card').forEach(function(card){
            var t=card.querySelector('.table-title'); if(!t)return;
            card.style.display=(!region||t.textContent.trim()===region)?'':'none';
        });
        c.querySelectorAll('.data-tables-grid, .charts-grid').forEach(function(grid){
            if (region) { grid.classList.add('filtered'); } else { grid.classList.remove('filtered'); }
        });
        if (currentTab === 'primeBended') {
            renderPrimeBended();
            if (document.getElementById('seChartsView').classList.contains('active')) renderPrimeBendedCharts();
        } else if (currentTab === 'primeSpandrel') {
            renderPrimeSpandrel();
            if (document.getElementById('tbChartsView').classList.contains('active')) renderPrimeSpandrelCharts();
        } else if (currentTab === 'steelDeck') {
            renderSteelDeck();
            if (document.getElementById('ecChartsView').classList.contains('active')) renderSteelDeckCharts();
        }
    }

    // ============================================================
    // PRIME BENDED - TABLE RENDERING
    // ============================================================

    function renderPrimeBendedTable(region) {
        var data=primeBendedData[region]||[];
        var catId='se'+region.charAt(0).toUpperCase()+region.slice(1)+'Cat';
        var catEl=document.getElementById(catId);
        var category = (catEl && catEl.value) || 'attainment';
        var bid='se'+region.charAt(0).toUpperCase()+region.slice(1)+'Body';
        var b=document.getElementById(bid); if(!b)return;
        
        // Update headers FIRST before checking data
        var headId='se'+region.charAt(0).toUpperCase()+region.slice(1)+'Head';
        var headEl=document.getElementById(headId);
        if(headEl){
            if(category==='attainment'){
                headEl.innerHTML='<tr><th>RANK</th><th>% ATTAINMENT</th><th>ACTUAL</th><th>BUDGET</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr>';
            } else {
                headEl.innerHTML='<tr><th>RANK</th><th>REVENUE</th><th>ACTUAL</th><th>PRICE/LF</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr>';
            }
        }
        
        b.innerHTML='';
        if(!data.length){b.innerHTML='<tr><td colspan="8" style="padding:2rem;text-align:center;color:#94a3b8;">No data available.</td></tr>';return;}
        // Filter data by selected category to prevent data overlap
        var filteredData = data.filter(function(d){ return d.category === category; });
        if(!filteredData.length){b.innerHTML='<tr><td colspan="8" style="padding:2rem;text-align:center;color:#94a3b8;">No data for this category.</td></tr>';return;}
        var sorted=filteredData.slice();
        if(category==='attainment'){
            sorted.sort(function(a,b){return (b.att||0)-(a.att||0);});
        } else {
            sorted.sort(function(a,b){return (b.margin||0)-(a.margin||0);});
        }
        // Always show TOP 3 per category per region
        var dd = sorted.slice(0,3);
        dd.forEach(function(d,i){
            var rl=i+1;
            var actions='<td><button class="de-btn de-btn-edit" onclick="openDashModal(\'se\',\''+d.uuid+'\')" style="padding:0.15rem 0.35rem;font-size:0.55rem;">✎</button> <button class="de-btn de-btn-delete" onclick="deleteDashRecord(\'se\',\''+d.uuid+'\')" style="padding:0.15rem 0.35rem;font-size:0.55rem;">✕</button></td>';
            var tr=document.createElement('tr');
            var name = d.n || '-';
            var area = d.a || (d.company && d.company !== 'N/A' ? d.company : '-');
            var position = d.pos || '-';
            if(category==='attainment'){
                var att = d.att || 0;
                var actual = d.actual || d.qtyInvoice || 0;
                var budget = d.budget || d.volume || 0;
                tr.innerHTML='<td>'+rl+'</td><td>'+Number(att).toFixed(2)+'</td><td>'+(Number.isInteger(actual)?actual.toLocaleString():actual.toLocaleString())+'</td><td>'+(Number.isInteger(budget)?budget.toLocaleString():budget.toLocaleString())+'</td><td>'+name+'</td><td>'+area+'</td><td>'+position+'</td>'+actions;
            } else {
                var revenue = d.revenue || 0;
                var actualCm = d.cm || d.grossAmount || 0;
                var pricelf = d.pricelf || 0;
                tr.innerHTML='<td>'+rl+'</td><td>'+fmt(revenue)+'</td><td>'+fmt(actualCm)+'</td><td>'+Number(pricelf).toFixed(2)+'</td><td>'+name+'</td><td>'+area+'</td><td>'+position+'</td>'+actions;
            }
            b.appendChild(tr);
        });
    }

    function renderPrimeBended() {
        var d=getPrimeBendedData();primeBendedData=d;
        renderPrimeBendedTable('south');renderPrimeBendedTable('nc');renderPrimeBendedTable('vis');renderPrimeBendedTable('min');
        var globalHighestAtt = {value: -Infinity, region: null};
        var globalHighestRev = {value: -Infinity, region: null};
        var totalRev = 0;
        Object.keys(d).forEach(function(r){
            var rd=d[r]||[];
            rd.forEach(function(x){
                var att = parseFloat(x.att) || 0;
                var rev = parseFloat(x.revenue) || 0;
                // Find highest % ATTAINMENT across all regions
                if (att > globalHighestAtt.value) { globalHighestAtt.value = att; globalHighestAtt.region = r; }
                // Find highest REVENUE amount across all regions
                if (rev > globalHighestRev.value) { globalHighestRev.value = rev; globalHighestRev.region = r; }
                totalRev += rev;
            });
        });
        var topAttRegion = globalHighestAtt.region && regionNames[globalHighestAtt.region] ? regionNames[globalHighestAtt.region] : '-';
        var topRevRegion = globalHighestRev.region && regionNames[globalHighestRev.region] ? regionNames[globalHighestRev.region] : '-';
        setText('seTopRegion', topAttRegion);
        setText('seHighestVolume', globalHighestAtt.value > -Infinity ? Number(globalHighestAtt.value).toFixed(2) + '%' : '0%');
        setText('seHighestRevenue', globalHighestRev.value > -Infinity ? fmt(globalHighestRev.value) : '₱0.00');
        setText('seMonthlySales', fmt(totalRev));
    }
    // ============================================================
    // Per-bar gradient: highest value = UGC brand color (#0B7A3B / #E31C23), lowest = light tint
    function getBarColors(data, maxValue, theme) {
        var colors = [];
        for (var i = 0; i < data.length; i++) {
            var val = parseFloat(data[i]) || 0;
            var intensity = maxValue > 0 ? Math.min(1, val / maxValue) : 0;
            intensity = Math.max(0.15, Math.min(1, intensity));
            if (theme === 'green') {
                // UGC Green #0B7A3B: rgb(11, 122, 59) -> light tint rgb(200, 235, 210)
                var rr = 11 + Math.round((200 - 11) * (1 - intensity));
                var gg = 122 + Math.round((235 - 122) * (1 - intensity));
                var bb = 59 + Math.round((210 - 59) * (1 - intensity));
                colors.push('rgb(' + rr + ',' + gg + ',' + bb + ')');
            } else {
                // UGC Red #E31C23: rgb(227, 28, 35) -> light tint rgb(245, 200, 200)
                var rr = 227 + Math.round((245 - 227) * (1 - intensity));
                var gg = 28 + Math.round((200 - 28) * (1 - intensity));
                var bb = 35 + Math.round((200 - 35) * (1 - intensity));
                colors.push('rgb(' + rr + ',' + gg + ',' + bb + ')');
            }
        }
        return colors;
    }

    // Gradient fill config for distributed bars
    function distributedGradientFill(theme) {
        if (theme === 'green') {
            return {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.3,
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 0.85,
                    stops: [0, 100]
                }
            };
        } else {
            return {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.3,
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 0.85,
                    stops: [0, 100]
                }
            };
        }
    }

    function renderSingleChart(el, chartKey, sorted, metric, metricName, valuePrefix, theme) {
        if (chartInstances[chartKey]) { chartInstances[chartKey].destroy(); delete chartInstances[chartKey]; }
        var categories = sorted.map(function(d){ return d.n || 'N/A'; });
        var seriesData;
        if (metric === 'att') {
            seriesData = sorted.map(function(d){ return Number(d.att || 0).toFixed(2); });
        } else if (metric === 'margin') {
            seriesData = sorted.map(function(d){ return parseFloat(d.margin || 0); });
        } else if (metric === 'growth') {
            seriesData = sorted.map(function(d){ return Number(d.growth || 0).toFixed(2); });
        } else {
            seriesData = sorted.map(function(d){ return d.revenue || 0; });
        }
        
        var maxVal = Math.max.apply(null, seriesData.map(function(v){ return Math.abs(parseFloat(v)||0); }));
        var barColors = getBarColors(seriesData, maxVal, theme);
        
        chartInstances[chartKey] = new ApexCharts(el, {
            chart: { type: 'bar', height: '100%', width: '100%', fontFamily: 'inherit', toolbar: { show: true }, foreColor: '#374151' },
            series: [{ name: metricName, data: seriesData }],
            xaxis: { categories: categories, labels: { style: { fontSize: '9px', fontWeight: 600 }, trim: true, maxHeight: 60, rotate: -20 } },
            colors: barColors,
            fill: distributedGradientFill(theme),
            plotOptions: { bar: { distributed: true, borderRadius: 4, columnWidth: '70%', horizontal: false, dataLabels: { position: 'top' } } },
            dataLabels: { enabled: true, formatter: function(v) { if (valuePrefix) { return valuePrefix + Number(v).toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 0}); } return Number(v).toFixed(2) + '%'; }, style: { fontSize: '8px', fontWeight: 700, colors: ['#1e293b'] } },
            yaxis: { labels: { formatter: function(v) { if (valuePrefix) { return valuePrefix + Number(v).toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 0}); } return Number(v).toFixed(2) + '%'; }, style: { fontSize: '9px' } } },
            grid: { borderColor: '#e2e8f0', strokeDashArray: 3 },
            legend: { show: false },
            tooltip: { y: { formatter: function(v) { if (valuePrefix) { return valuePrefix + Number(v).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}); } return Number(v).toFixed(2) + '%'; } } }
        });
        chartInstances[chartKey].render();
    }

    // Variant of renderSingleChart that uses sales office as category label instead of name
    function renderSingleChartByOffice(el, chartKey, sorted, metric, metricName, valuePrefix, theme) {
        if (chartInstances[chartKey]) { chartInstances[chartKey].destroy(); delete chartInstances[chartKey]; }
        var categories = sorted.map(function(d){ return d.office || d.n || 'N/A'; });
        var seriesData;
        if (metric === 'att') {
            seriesData = sorted.map(function(d){ return Number(d.att || 0).toFixed(2); });
        } else if (metric === 'margin') {
            seriesData = sorted.map(function(d){ return parseFloat(d.margin || 0); });
        } else if (metric === 'growth') {
            seriesData = sorted.map(function(d){ return Number(d.growth || 0).toFixed(2); });
        } else {
            seriesData = sorted.map(function(d){ return d.revenue || 0; });
        }
        
        var maxVal = Math.max.apply(null, seriesData.map(function(v){ return Math.abs(parseFloat(v)||0); }));
        var barColors = getBarColors(seriesData, maxVal, theme);
        
        chartInstances[chartKey] = new ApexCharts(el, {
            chart: { type: 'bar', height: '100%', width: '100%', fontFamily: 'inherit', toolbar: { show: true }, foreColor: '#374151' },
            series: [{ name: metricName, data: seriesData }],
            xaxis: { categories: categories, labels: { style: { fontSize: '9px', fontWeight: 600 }, trim: true, maxHeight: 60, rotate: -20 } },
            colors: barColors,
            fill: distributedGradientFill(theme),
            plotOptions: { bar: { distributed: true, borderRadius: 4, columnWidth: '70%', horizontal: false, dataLabels: { position: 'top' } } },
            dataLabels: { enabled: true, formatter: function(v) { if (valuePrefix) { return valuePrefix + Number(v).toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 0}); } return Number(v).toFixed(2) + '%'; }, style: { fontSize: '8px', fontWeight: 700, colors: ['#1e293b'] } },
            yaxis: { labels: { formatter: function(v) { if (valuePrefix) { return valuePrefix + Number(v).toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 0}); } return Number(v).toFixed(2) + '%'; }, style: { fontSize: '9px' } } },
            grid: { borderColor: '#e2e8f0', strokeDashArray: 3 },
            legend: { show: false },
            tooltip: { y: { formatter: function(v) { if (valuePrefix) { return valuePrefix + Number(v).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}); } return Number(v).toFixed(2) + '%'; } } }
        });
        chartInstances[chartKey].render();
    }

    // Variant of renderSingleChart that handles amount-based metrics (volume, grossAmount) without percentage formatting
    function renderSingleChartByAmount(el, chartKey, sorted, metric, metricName, valuePrefix, theme) {
        if (chartInstances[chartKey]) { chartInstances[chartKey].destroy(); delete chartInstances[chartKey]; }
        var categories = sorted.map(function(d){ return d.n || 'N/A'; });
        var seriesData = sorted.map(function(d){ return parseFloat(d[metric] || 0); });
        
        var maxVal = Math.max.apply(null, seriesData.map(function(v){ return Math.abs(v||0); }));
        var barColors = getBarColors(seriesData, maxVal, theme);
        
        chartInstances[chartKey] = new ApexCharts(el, {
            chart: { type: 'bar', height: '100%', width: '100%', fontFamily: 'inherit', toolbar: { show: true }, foreColor: '#374151' },
            series: [{ name: metricName, data: seriesData }],
            xaxis: { categories: categories, labels: { style: { fontSize: '9px', fontWeight: 600 }, trim: true, maxHeight: 60, rotate: -20 } },
            colors: barColors,
            fill: distributedGradientFill(theme),
            plotOptions: { bar: { distributed: true, borderRadius: 4, columnWidth: '70%', horizontal: false, dataLabels: { position: 'top' } } },
            dataLabels: { enabled: true, formatter: function(v) { if (valuePrefix) { return valuePrefix + Number(v).toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 0}); } return Number(v).toLocaleString(); }, style: { fontSize: '8px', fontWeight: 700, colors: ['#1e293b'] } },
            yaxis: { labels: { formatter: function(v) { if (valuePrefix) { return valuePrefix + Number(v).toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 0}); } return Number(v).toLocaleString(); }, style: { fontSize: '9px' } } },
            grid: { borderColor: '#e2e8f0', strokeDashArray: 3 },
            legend: { show: false },
            tooltip: { y: { formatter: function(v) { if (valuePrefix) { return valuePrefix + Number(v).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}); } return Number(v).toLocaleString(); } } }
        });
        chartInstances[chartKey].render();
    }

    // Comparative bar chart with TWO series - use per-series gradient, no distributed to keep legend accurate
    function renderComparativeChart(el, chartKey, sorted, series1Name, series1Data, series2Name, series2Data, categories, theme1, theme2) {
        if (chartInstances[chartKey]) { chartInstances[chartKey].destroy(); delete chartInstances[chartKey]; }
        
        var allData = series1Data.concat(series2Data);
        var maxVal = Math.max.apply(null, allData.map(function(v){ return Math.abs(v||0); }));
        var colors1 = getBarColors(series1Data, maxVal, theme1);
        var colors2 = getBarColors(series2Data, maxVal, theme2);
        
        // Create per-series data with color arrays using the series colors API
        var series = [
            { name: series1Name, data: series1Data.map(function(v, i) { return { x: categories[i], y: v, fillColor: colors1[i] }; }) },
            { name: series2Name, data: series2Data.map(function(v, i) { return { x: categories[i], y: v, fillColor: colors2[i] }; }) }
        ];
        
        chartInstances[chartKey] = new ApexCharts(el, {
            chart: { type: 'bar', height: '100%', width: '100%', fontFamily: 'inherit', toolbar: { show: true }, foreColor: '#374151' },
            series: series,
            xaxis: { categories: categories, labels: { style: { fontSize: '9px', fontWeight: 600 }, trim: true, maxHeight: 60, rotate: -20 } },
            colors: [getBarColors([1], 1, theme1)[0], getBarColors([1], 1, theme2)[0]],
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.3,
                    inverseColors: false,
                    opacityFrom: 0.9,
                    opacityTo: 0.8,
                    stops: [0, 100]
                }
            },
            plotOptions: { bar: { borderRadius: 4, columnWidth: '60%', horizontal: false, dataLabels: { position: 'top' } } },
            dataLabels: { enabled: true, formatter: function(v) { return Number(v).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }, style: { fontSize: '8px', fontWeight: 700, colors: ['#1e293b'] } },
            yaxis: { labels: { formatter: function(v) { return Number(v).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }, style: { fontSize: '9px' } } },
            grid: { borderColor: '#e2e8f0', strokeDashArray: 3 },
            legend: { position: 'top', horizontalAlign: 'center', fontSize: '10px', fontWeight: 600, markers: { width: 10, height: 10, radius: 2 } },
            tooltip: { y: { formatter: function(v) { return '₱' + Number(v).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}); } } }
        });
        chartInstances[chartKey].render();
    }

    function renderPrimeBendedRegionCharts(region) {
        var catId = 'se' + region.charAt(0).toUpperCase() + region.slice(1) + 'ChartCat';
        var catEl = document.getElementById(catId);
        var category = (catEl && catEl.value) || 'all';
        
        var attEl = document.getElementById('se' + region.charAt(0).toUpperCase() + region.slice(1) + 'AttChart');
        var revEl = document.getElementById('se' + region.charAt(0).toUpperCase() + region.slice(1) + 'RevChart');
        var rowEl = document.getElementById('se' + region.charAt(0).toUpperCase() + region.slice(1) + 'ChartRow');
        
        var data = primeBendedData[region] || [];
        
        var regionFilter = document.getElementById('sideRegionFilter');
        var isRegionFiltered = regionFilter && regionFilter.value && regionFilter.value.toUpperCase() === regionNames[region].toUpperCase();
        if (!isRegionFiltered && regionFilter && regionFilter.value) {
            if (attEl) { attEl.innerHTML = ''; }
            if (revEl) { revEl.innerHTML = ''; }
            return;
        }
        
        if (!data.length) {
            if (attEl) attEl.innerHTML = '<div style="padding:2rem;text-align:center;color:#94a3b8;font-size:0.75rem;">No data available.</div>';
            if (revEl) revEl.innerHTML = '<div style="padding:2rem;text-align:center;color:#94a3b8;font-size:0.75rem;">No data available.</div>';
            return;
        }
        
        // Destroy existing chart instances for this region
        ['Att','Rev'].forEach(function(suffix) {
            var key = 'se' + region.charAt(0).toUpperCase() + region.slice(1) + suffix;
            if (chartInstances[key]) { chartInstances[key].destroy(); delete chartInstances[key]; }
        });
        
        // FILTER data by category FIRST before sorting - critical fix for accuracy
        var filteredByAtt = data.filter(function(d){ return d.category === 'attainment'; });
        var filteredByMargin = data.filter(function(d){ return d.category === 'margin'; });
        
        var sortedByAtt = filteredByAtt.slice().sort(function(a,b){ return (b.att||0) - (a.att||0); });
        var sortedByRev = filteredByMargin.slice().sort(function(a,b){ return (b.revenue||0) - (a.revenue||0); });
        var sortedByMargin = filteredByMargin.slice().sort(function(a,b){ return (b.margin||0) - (a.margin||0); });
        
        if (category === 'all') {
            // Show TWO charts side-by-side
            if (rowEl) { rowEl.style.flexDirection = 'row'; }
            // Show both .chart-half containers
            if (attEl) {
                var attHalf = attEl.closest('.chart-half');
                if (attHalf) attHalf.style.display = '';
                var attLabel = attEl.parentElement ? attEl.parentElement.querySelector('.chart-label') : null;
                if (attLabel) attLabel.textContent = 'HIGHEST % ATTAINMENT OVER BUDGET';
            }
            if (revEl) {
                var revHalf = revEl.closest('.chart-half');
                if (revHalf) revHalf.style.display = '';
                var revLabel = revEl.parentElement ? revEl.parentElement.querySelector('.chart-label') : null;
                if (revLabel) revLabel.textContent = 'HIGHEST % CONTRIBUTION MARGIN (REVENUE)';
            }
            renderSingleChart(attEl, 'se' + region.charAt(0).toUpperCase() + region.slice(1) + 'Att', sortedByAtt, 'att', '% Attainment', '', 'green');
            // Contribution Margin uses RED gradient - highest = solid red, lowest = light red
            renderSingleChart(revEl, 'se' + region.charAt(0).toUpperCase() + region.slice(1) + 'Rev', sortedByRev, 'revenue', 'Revenue', '₱', 'red');
        } else if (category === 'attainment') {
            // Show ONE chart - % Attainment (full width)
            if (rowEl) { rowEl.style.flexDirection = 'row'; }
            if (attEl) {
                var attHalf = attEl.closest('.chart-half');
                if (attHalf) attHalf.style.display = '';
                var attLabel = attEl.parentElement ? attEl.parentElement.querySelector('.chart-label') : null;
                if (attLabel) attLabel.textContent = 'HIGHEST % ATTAINMENT OVER BUDGET';
            }
            if (revEl) {
                var revHalf = revEl.closest('.chart-half');
                if (revHalf) revHalf.style.display = 'none';
            }
            renderSingleChart(attEl, 'se' + region.charAt(0).toUpperCase() + region.slice(1) + 'Att', sortedByAtt, 'att', '% Attainment', '', 'green');
        } else if (category === 'margin') {
            // Show ONE chart - Contribution Margin (full width)
            if (rowEl) { rowEl.style.flexDirection = 'row'; }
            if (attEl) {
                var attHalf = attEl.closest('.chart-half');
                if (attHalf) attHalf.style.display = 'none';
            }
            if (revEl) {
                var revHalf = revEl.closest('.chart-half');
                if (revHalf) revHalf.style.display = '';
                var revLabel = revEl.parentElement ? revEl.parentElement.querySelector('.chart-label') : null;
                if (revLabel) revLabel.textContent = 'HIGHEST % CONTRIBUTION MARGIN (REVENUE)';
            }
            // Use revenue data instead of margin field (margin field is always 0 - hidden in form)
            renderSingleChart(revEl, 'se' + region.charAt(0).toUpperCase() + region.slice(1) + 'Rev', sortedByRev, 'revenue', 'Revenue', '₱', 'red');
        }
    }

    function renderPrimeBendedCharts() {
        renderPrimeBendedRegionCharts('south');
        renderPrimeBendedRegionCharts('nc');
        renderPrimeBendedRegionCharts('vis');
        renderPrimeBendedRegionCharts('min');
    }

    // Sync table category filter to chart category filter and re-render charts
    window.syncSeChartFilter = function(region) {
        var tableCatId = 'se' + region.charAt(0).toUpperCase() + region.slice(1) + 'Cat';
        var chartCatId = 'se' + region.charAt(0).toUpperCase() + region.slice(1) + 'ChartCat';
        var tableCatEl = document.getElementById(tableCatId);
        var chartCatEl = document.getElementById(chartCatId);
        if (tableCatEl && chartCatEl) {
            chartCatEl.value = tableCatEl.value;
        }
        // Re-render charts if charts view is active
        if (document.getElementById('seChartsView').classList.contains('active')) {
            renderPrimeBendedCharts();
        }
    };

    window.syncTbChartFilter = function(region) {
        var tableCatId = 'tb' + region.charAt(0).toUpperCase() + region.slice(1) + 'Cat';
        var chartCatId = 'tb' + region.charAt(0).toUpperCase() + region.slice(1) + 'ChartCat';
        var tableCatEl = document.getElementById(tableCatId);
        var chartCatEl = document.getElementById(chartCatId);
        if (tableCatEl && chartCatEl) {
            chartCatEl.value = tableCatEl.value;
        }
        // Re-render charts if charts view is active
        if (document.getElementById('tbChartsView').classList.contains('active')) {
            renderPrimeSpandrelCharts();
        }
    };

    window.syncEcChartFilter = function(region) {
        var tableCatId = 'ec' + region.charAt(0).toUpperCase() + region.slice(1) + 'Cat';
        var chartCatId = 'ec' + region.charAt(0).toUpperCase() + region.slice(1) + 'ChartCat';
        var tableCatEl = document.getElementById(tableCatId);
        var chartCatEl = document.getElementById(chartCatId);
        if (tableCatEl && chartCatEl) {
            chartCatEl.value = tableCatEl.value;
        }
        // Re-render charts if charts view is active
        if (document.getElementById('ecChartsView').classList.contains('active')) {
            renderSteelDeckCharts();
        }
    };

    // ============================================================
    // PRIME SPANDREL - TABLE RENDERING
    // ============================================================

    function renderPrimeSpandrelTable(region) {
        var data=primeSpandrelData[region]||[];
        var catId='tb'+region.charAt(0).toUpperCase()+region.slice(1)+'Cat';
        var catEl=document.getElementById(catId);
        var category = (catEl && catEl.value) || 'attainment';
        var bid='tb'+region.charAt(0).toUpperCase()+region.slice(1)+'Body';
        var b=document.getElementById(bid); if(!b)return;
        
        // Update headers FIRST before checking data
        var headId='tb'+region.charAt(0).toUpperCase()+region.slice(1)+'Head';
        var headEl=document.getElementById(headId);
        if(headEl){
            if(category==='attainment'){
                headEl.innerHTML='<tr><th>SALES OFFICE</th><th>% ATTAINMENT</th><th>ACTUAL</th><th>BUDGET</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr>';
            } else {
                headEl.innerHTML='<tr><th>SALES OFFICE</th><th>LAST MONTH</th><th>CURRENT MONTH</th><th>% GROWTH</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr>';
            }
        }
        
        b.innerHTML='';
        if(!data.length){b.innerHTML='<tr><td colspan="8" style="padding:2rem;text-align:center;color:#94a3b8;">No data available.</td></tr>';return;}
        // Filter data by selected category to prevent data overlap
        var filteredData = data.filter(function(d){ return d.category === category; });
        if(!filteredData.length){b.innerHTML='<tr><td colspan="8" style="padding:2rem;text-align:center;color:#94a3b8;">No data for this category.</td></tr>';return;}
        var sorted=filteredData.slice();
        if(category==='attainment'){
            sorted.sort(function(a,b){return (b.att||0)-(a.att||0);});
        } else {
            sorted.sort(function(a,b){return (b.growth||0)-(a.growth||0);});
        }
        // Always show TOP 3 per category per region
        var dd = sorted.slice(0,3);
        dd.forEach(function(d,i){
            var actions='<td><button class="de-btn de-btn-edit" onclick="openDashModal(\'tb\',\''+d.uuid+'\')" style="padding:0.15rem 0.35rem;font-size:0.55rem;">✎</button> <button class="de-btn de-btn-delete" onclick="deleteDashRecord(\'tb\',\''+d.uuid+'\')" style="padding:0.15rem 0.35rem;font-size:0.55rem;">✕</button></td>';
            var tr=document.createElement('tr');
            var office = d.office || '-';
            var name = d.n || '-';
            var area = d.a || '-';
            var position = d.pos || '-';
            if(category==='attainment'){
                var att = d.att || 0;
                var actual = d.actual || d.currentMonth || 0;
                var budget = d.budget || 0;
                tr.innerHTML='<td>'+office+'</td><td>'+Number(att).toFixed(2)+'</td><td>'+(Number.isInteger(actual)?actual.toLocaleString():actual.toLocaleString())+'</td><td>'+(Number.isInteger(budget)?budget.toLocaleString():budget.toLocaleString())+'</td><td>'+name+'</td><td>'+area+'</td><td>'+position+'</td>'+actions;
            } else {
                var lastMonth = d.lastMonth || 0;
                var currentMonth = d.currentMonth || 0;
                var growth = d.growth || 0;
                tr.innerHTML='<td>'+office+'</td><td>'+(Number.isInteger(lastMonth)?lastMonth.toLocaleString():lastMonth.toLocaleString())+'</td><td>'+(Number.isInteger(currentMonth)?currentMonth.toLocaleString():currentMonth.toLocaleString())+'</td><td>'+Number(growth).toFixed(2)+'</td><td>'+name+'</td><td>'+area+'</td><td>'+position+'</td>'+actions;
            }
            b.appendChild(tr);
        });
    }

    function renderPrimeSpandrel() {
        var d=getPrimeSpandrelData();primeSpandrelData=d;
        renderPrimeSpandrelTable('south');renderPrimeSpandrelTable('nc');renderPrimeSpandrelTable('vis');renderPrimeSpandrelTable('min');
        
        // Analyze % ATTAINMENT and % GROWTH across all four regions
        var globalHighestAtt = {value: -Infinity, region: null};
        var globalHighestGrowth = {value: -Infinity, region: null};
        
        // Collect all growth values for AVS GROWTH (average growth)
        var allGrowthValues = [];
        
        Object.keys(d).forEach(function(r){
            var rd=d[r]||[];
            
            // Separate records by category
            var attRecords = rd.filter(function(x){ return x.category === 'attainment'; });
            var growthRecords = rd.filter(function(x){ return x.category === 'margin'; });
            
            // Find highest % ATTAINMENT in this region
            var regionHighestAtt = attRecords.reduce(function(max, x){ 
                var att = parseFloat(x.att) || 0; 
                return att > max ? att : max; 
            }, 0);
            
            // Find highest % GROWTH in this region
            var regionHighestGrowth = growthRecords.reduce(function(max, x){ 
                var growth = parseFloat(x.growth) || 0; 
                return growth > max ? growth : max; 
            }, 0);
            
            // Track global highest attainment
            if (regionHighestAtt > globalHighestAtt.value) { 
                globalHighestAtt.value = regionHighestAtt; 
                globalHighestAtt.region = r;
            }
            
            // Track global highest growth
            if (regionHighestGrowth > globalHighestGrowth.value) { 
                globalHighestGrowth.value = regionHighestGrowth; 
                globalHighestGrowth.region = r;
            }
            
            // Collect all growth values for average computation
            growthRecords.forEach(function(x){
                allGrowthValues.push(parseFloat(x.growth) || 0);
            });
        });
        
        // TOP REGION: Show which region is top in % ATTAINMENT and which is top in % GROWTH
        var topAttRegionName = globalHighestAtt.region && regionNames[globalHighestAtt.region] ? regionNames[globalHighestAtt.region] : null;
        var topGrowthRegionName = globalHighestGrowth.region && regionNames[globalHighestGrowth.region] ? regionNames[globalHighestGrowth.region] : null;
        
        // Check if there's any actual data before displaying KPIs
        var hasAnyData = Object.keys(d).some(function(r) { return d[r] && d[r].length > 0; });
        if (hasAnyData && topAttRegionName) {
            setText('tbTopRegion', topAttRegionName);
        } else {
            setText('tbTopRegion', '-');
        }
        
        // HIGHEST ATTAINMENT: Display the highest % ATTAINMENT across all regions
        setText('tbHighestVolume', globalHighestAtt.value > -Infinity ? Number(globalHighestAtt.value).toFixed(2) + '%' : '0%');
        
        // HIGHEST GROWTH: Display the highest % GROWTH across all regions
        setText('tbHighestRevenue', globalHighestGrowth.value > -Infinity ? Number(globalHighestGrowth.value).toFixed(2) + '%' : '0%');
        
        // AVS GROWTH: Compute average of all % GROWTH values across all regions
        var avgGrowth = 0;
        if (allGrowthValues.length > 0) {
            var sumGrowth = allGrowthValues.reduce(function(s, v){ return s + v; }, 0);
            avgGrowth = sumGrowth / allGrowthValues.length;
        }
        setText('tbMonthlySales', Number(avgGrowth).toFixed(2) + '%');
    }

    // ============================================================
    // PRIME SPANDREL - CHART RENDERING
    // ============================================================
    function renderPrimeSpandrelRegionCharts(region) {
        var catId = 'tb' + region.charAt(0).toUpperCase() + region.slice(1) + 'ChartCat';
        var catEl = document.getElementById(catId);
        var category = (catEl && catEl.value) || 'all';
        
        var attEl = document.getElementById('tb' + region.charAt(0).toUpperCase() + region.slice(1) + 'AttChart');
        var growthEl = document.getElementById('tb' + region.charAt(0).toUpperCase() + region.slice(1) + 'GrowthChart');
        var rowEl = document.getElementById('tb' + region.charAt(0).toUpperCase() + region.slice(1) + 'ChartRow');
        
        var data = primeSpandrelData[region] || [];
        
        var regionFilter = document.getElementById('sideRegionFilter');
        var isRegionFiltered = regionFilter && regionFilter.value && regionFilter.value.toUpperCase() === regionNames[region].toUpperCase();
        if (!isRegionFiltered && regionFilter && regionFilter.value) {
            if (attEl) { attEl.innerHTML = ''; }
            if (growthEl) { growthEl.innerHTML = ''; }
            return;
        }
        
        if (!data.length) {
            if (attEl) attEl.innerHTML = '<div style="padding:2rem;text-align:center;color:#94a3b8;font-size:0.75rem;">No data available.</div>';
            if (growthEl) growthEl.innerHTML = '<div style="padding:2rem;text-align:center;color:#94a3b8;font-size:0.75rem;">No data available.</div>';
            return;
        }
        
        // Destroy existing chart instances for this region
        ['Att','Growth'].forEach(function(suffix) {
            var key = 'tb' + region.charAt(0).toUpperCase() + region.slice(1) + suffix;
            if (chartInstances[key]) { chartInstances[key].destroy(); delete chartInstances[key]; }
        });
        
        // FILTER data by category FIRST before sorting
        var filteredByAtt = data.filter(function(d){ return d.category === 'attainment'; });
        var filteredByGrowth = data.filter(function(d){ return d.category === 'margin'; });
        
        var sortedByAtt = filteredByAtt.slice().sort(function(a,b){ return (b.att||0) - (a.att||0); });
        var sortedByGrowth = filteredByGrowth.slice().sort(function(a,b){ return (b.growth||0) - (a.growth||0); });
        
        // --- PREPARE COMPARATIVE DATA ---
        // For ATTAINMENT chart: Use sales office as category, show ACTUAL vs BUDGET as 2 series
        var attCategories = sortedByAtt.map(function(d){ return d.office || d.n || 'N/A'; });
        var attActualData = sortedByAtt.map(function(d){ return parseFloat(d.actual) || 0; });
        var attBudgetData = sortedByAtt.map(function(d){ return parseFloat(d.budget) || 0; });
        
        // For GROWTH chart: Use sales office as category, show LAST MONTH vs CURRENT MONTH as 2 series
        var growthCategories = sortedByGrowth.map(function(d){ return d.office || d.n || 'N/A'; });
        var growthLastMonthData = sortedByGrowth.map(function(d){ return parseFloat(d.lastMonth) || 0; });
        var growthCurrentMonthData = sortedByGrowth.map(function(d){ return parseFloat(d.currentMonth) || 0; });
        
        if (category === 'all') {
            // Show TWO charts side-by-side - both as COMPARATIVE charts
            if (rowEl) { rowEl.style.flexDirection = 'row'; }
            if (attEl) {
                var attHalf = attEl.closest('.chart-half');
                if (attHalf) attHalf.style.display = '';
                var attLabel = attEl.parentElement ? attEl.parentElement.querySelector('.chart-label') : null;
                if (attLabel) attLabel.textContent = 'HIGHEST % ATTAINMENT OVER BUDGET';
            }
            if (growthEl) {
                var growthHalf = growthEl.closest('.chart-half');
                if (growthHalf) growthHalf.style.display = '';
                var growthLabel = growthEl.parentElement ? growthEl.parentElement.querySelector('.chart-label') : null;
                if (growthLabel) growthLabel.textContent = 'HIGHEST % GROWTH VS LM';
            }
            // ATTAINMENT: Comparative chart - ACTUAL (GREEN) vs BUDGET (RED)
            renderComparativeChart(attEl, 'tb' + region.charAt(0).toUpperCase() + region.slice(1) + 'Att',
                sortedByAtt, 'Actual', attActualData, 'Budget', attBudgetData, attCategories, 'green', 'red');
            // GROWTH: Comparative chart - LAST MONTH (RED) vs CURRENT MONTH (GREEN)
            renderComparativeChart(growthEl, 'tb' + region.charAt(0).toUpperCase() + region.slice(1) + 'Growth',
                sortedByGrowth, 'Last Month', growthLastMonthData, 'Current Month', growthCurrentMonthData, growthCategories, 'red', 'green');
        } else if (category === 'attainment') {
            // Show ONE comparative chart - ACTUAL vs BUDGET (full width)
            if (rowEl) { rowEl.style.flexDirection = 'row'; }
            if (attEl) {
                var attHalf = attEl.closest('.chart-half');
                if (attHalf) attHalf.style.display = '';
                var attLabel = attEl.parentElement ? attEl.parentElement.querySelector('.chart-label') : null;
                if (attLabel) attLabel.textContent = 'HIGHEST % ATTAINMENT OVER BUDGET';
            }
            if (growthEl) {
                var growthHalf = growthEl.closest('.chart-half');
                if (growthHalf) growthHalf.style.display = 'none';
            }
            renderComparativeChart(attEl, 'tb' + region.charAt(0).toUpperCase() + region.slice(1) + 'Att',
                sortedByAtt, 'Actual', attActualData, 'Budget', attBudgetData, attCategories, 'green', 'red');
        } else if (category === 'growth') {
            // Show ONE comparative chart - LAST MONTH vs CURRENT MONTH (full width)
            if (rowEl) { rowEl.style.flexDirection = 'row'; }
            if (attEl) {
                var attHalf = attEl.closest('.chart-half');
                if (attHalf) attHalf.style.display = 'none';
            }
            if (growthEl) {
                var growthHalf = growthEl.closest('.chart-half');
                if (growthHalf) growthHalf.style.display = '';
                var growthLabel = growthEl.parentElement ? growthEl.parentElement.querySelector('.chart-label') : null;
                if (growthLabel) growthLabel.textContent = 'HIGHEST % GROWTH VS LM';
            }
            renderComparativeChart(growthEl, 'tb' + region.charAt(0).toUpperCase() + region.slice(1) + 'Growth',
                sortedByGrowth, 'Last Month', growthLastMonthData, 'Current Month', growthCurrentMonthData, growthCategories, 'red', 'green');
        }
    }

    function renderPrimeSpandrelCharts() {
        renderPrimeSpandrelRegionCharts('south');
        renderPrimeSpandrelRegionCharts('nc');
        renderPrimeSpandrelRegionCharts('vis');
        renderPrimeSpandrelRegionCharts('min');
    }

    // ============================================================
    // STEEL DECK - TABLE RENDERING
    // ============================================================

    function renderSteelDeckTable(region) {
        var data=steelDeckData[region]||[];
        var catId='ec'+region.charAt(0).toUpperCase()+region.slice(1)+'Cat';
        var catEl=document.getElementById(catId);
        var category = (catEl && catEl.value) || 'volume';
        var bid='ec'+region.charAt(0).toUpperCase()+region.slice(1)+'Body';
        var b=document.getElementById(bid); if(!b)return;
        
        // Update headers FIRST before checking data
        var headId='ec'+region.charAt(0).toUpperCase()+region.slice(1)+'Head';
        var headEl=document.getElementById(headId);
        if(headEl){
            if(category==='volume'){
                headEl.innerHTML='<tr><th>RANK</th><th>ACTUAL VOLUME</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr>';
            } else {
                headEl.innerHTML='<tr><th>RANK</th><th>ACTUAL CM</th><th>NAME</th><th>AREA</th><th>POSITION</th><th>ACTION</th></tr>';
            }
        }
        
        b.innerHTML='';
        if(!data.length){b.innerHTML='<tr><td colspan="6" style="padding:2rem;text-align:center;color:#94a3b8;">No data available.</td></tr>';return;}
        // Filter data by selected category to prevent data overlap
        var filteredData = data.filter(function(d){ return d.category === category; });
        if(!filteredData.length){b.innerHTML='<tr><td colspan="6" style="padding:2rem;text-align:center;color:#94a3b8;">No data for this category.</td></tr>';return;}
        var sorted=filteredData.slice();
        if(category==='volume'){
            sorted.sort(function(a,b){return (b.volume||0)-(a.volume||0);});
        } else {
            sorted.sort(function(a,b){return (b.grossAmount||0)-(a.grossAmount||0);});
        }
        // Always show TOP 3 per category per region
        var dd = sorted.slice(0,3);
        dd.forEach(function(d,i){
            var rl=i+1;
            var actions='<td><button class="de-btn de-btn-edit" onclick="openDashModal(\'ec\',\''+d.uuid+'\')" style="padding:0.15rem 0.35rem;font-size:0.55rem;">✎</button> <button class="de-btn de-btn-delete" onclick="deleteDashRecord(\'ec\',\''+d.uuid+'\')" style="padding:0.15rem 0.35rem;font-size:0.55rem;">✕</button></td>';
            var tr=document.createElement('tr');
            var name = d.n || '-';
            var area = d.a || (d.company && d.company !== 'N/A' ? d.company : '-');
            var position = d.pos || '-';
            if(category==='volume'){
                var actualVol = d.volume || 0;
                tr.innerHTML='<td>'+rl+'</td><td>'+(Number.isInteger(actualVol)?actualVol.toLocaleString():actualVol.toLocaleString())+'</td><td>'+name+'</td><td>'+area+'</td><td>'+position+'</td>'+actions;
            } else {
                var actualCm = d.grossAmount || 0;
                tr.innerHTML='<td>'+rl+'</td><td>'+fmt(actualCm)+'</td><td>'+name+'</td><td>'+area+'</td><td>'+position+'</td>'+actions;
            }
            b.appendChild(tr);
        });
    }

    function renderSteelDeck() {
        var d=getSteelDeckData();steelDeckData=d;
        renderSteelDeckTable('south');renderSteelDeckTable('nc');renderSteelDeckTable('vis');renderSteelDeckTable('min');
        var topVolRegion = null;
        var topCmRegion = null;
        var topVolVal = -Infinity;
        var topCmVal = -Infinity;
        var combinedVol = 0;
        var combinedCm = 0;
        Object.keys(d).forEach(function(r){
            var rd=d[r]||[];
            var regVol = 0;
            var regCm = 0;
            rd.forEach(function(x){
                var vol = parseFloat(x.volume) || 0;
                var cm = parseFloat(x.grossAmount) || 0;
                regVol += vol;
                regCm += cm;
                combinedVol += vol;
                combinedCm += cm;
            });
            if (regVol > topVolVal && regVol > 0) { topVolVal = regVol; topVolRegion = r; }
            if (regCm > topCmVal && regCm > 0) { topCmVal = regCm; topCmRegion = r; }
        });
        setText('ecTopRegion',topVolRegion && regionNames[topVolRegion] ? regionNames[topVolRegion] : '-');
        setText('ecHighestVolume',topCmRegion && regionNames[topCmRegion] ? regionNames[topCmRegion] : '-');
        setText('ecHighestRevenue',combinedVol.toLocaleString());
        setText('ecMonthlySales',combinedCm.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}));
    }

    // ============================================================
    // STEEL DECK (ELITE CIRCLE) - CHART RENDERING
    // ============================================================
    function renderSteelDeckRegionCharts(region) {
        var catId = 'ec' + region.charAt(0).toUpperCase() + region.slice(1) + 'ChartCat';
        var catEl = document.getElementById(catId);
        var category = (catEl && catEl.value) || 'all';
        
        var volEl = document.getElementById('ec' + region.charAt(0).toUpperCase() + region.slice(1) + 'VolChart');
        var cmEl = document.getElementById('ec' + region.charAt(0).toUpperCase() + region.slice(1) + 'CmChart');
        var rowEl = document.getElementById('ec' + region.charAt(0).toUpperCase() + region.slice(1) + 'ChartRow');
        
        var data = steelDeckData[region] || [];
        
        var regionFilter = document.getElementById('sideRegionFilter');
        var isRegionFiltered = regionFilter && regionFilter.value && regionFilter.value.toUpperCase() === regionNames[region].toUpperCase();
        if (!isRegionFiltered && regionFilter && regionFilter.value) {
            if (volEl) { volEl.innerHTML = ''; }
            if (cmEl) { cmEl.innerHTML = ''; }
            return;
        }
        
        if (!data.length) {
            if (volEl) volEl.innerHTML = '<div style="padding:2rem;text-align:center;color:#94a3b8;font-size:0.75rem;">No data available.</div>';
            if (cmEl) cmEl.innerHTML = '<div style="padding:2rem;text-align:center;color:#94a3b8;font-size:0.75rem;">No data available.</div>';
            return;
        }
        
        // Destroy existing chart instances for this region
        ['Vol','Cm'].forEach(function(suffix) {
            var key = 'ec' + region.charAt(0).toUpperCase() + region.slice(1) + suffix;
            if (chartInstances[key]) { chartInstances[key].destroy(); delete chartInstances[key]; }
        });
        
        // FILTER data by category FIRST before sorting
        var filteredByVol = data.filter(function(d){ return d.category === 'volume'; });
        var filteredByCm = data.filter(function(d){ return d.category === 'margin'; });
        
        var sortedByVol = filteredByVol.slice().sort(function(a,b){ return (b.volume||0) - (a.volume||0); });
        var sortedByCm = filteredByCm.slice().sort(function(a,b){ return (b.grossAmount||0) - (a.grossAmount||0); });
        
        if (category === 'all') {
            // Show TWO charts side-by-side
            if (rowEl) { rowEl.style.flexDirection = 'row'; }
            if (volEl) {
                var volHalf = volEl.closest('.chart-half');
                if (volHalf) volHalf.style.display = '';
                var volLabel = volEl.parentElement ? volEl.parentElement.querySelector('.chart-label') : null;
                if (volLabel) volLabel.textContent = 'HIGHEST VOLUME CONTRIBUTOR PER REGION';
            }
            if (cmEl) {
                var cmHalf = cmEl.closest('.chart-half');
                if (cmHalf) cmHalf.style.display = '';
                var cmLabel = cmEl.parentElement ? cmEl.parentElement.querySelector('.chart-label') : null;
                if (cmLabel) cmLabel.textContent = 'HIGHEST CONTRIBUTION MARGIN PER REGION';
            }
            // VOLUME chart: Show volume values (use renderSingleChartByAmount with green gradient)
            if (volEl && sortedByVol.length) {
                renderSingleChartByAmount(volEl, 'ec' + region.charAt(0).toUpperCase() + region.slice(1) + 'Vol',
                    sortedByVol, 'volume', 'Volume', '', 'green');
            } else if (volEl) {
                volEl.innerHTML = '<div style="padding:2rem;text-align:center;color:#94a3b8;font-size:0.75rem;">No volume data.</div>';
            }
            // CM chart: Show contribution margin values with red gradient
            if (cmEl && sortedByCm.length) {
                renderSingleChartByAmount(cmEl, 'ec' + region.charAt(0).toUpperCase() + region.slice(1) + 'Cm',
                    sortedByCm, 'grossAmount', 'Contribution Margin', '₱', 'red');
            } else if (cmEl) {
                cmEl.innerHTML = '<div style="padding:2rem;text-align:center;color:#94a3b8;font-size:0.75rem;">No CM data.</div>';
            }
        } else if (category === 'volume') {
            // Show ONE chart - Volume (full width)
            if (rowEl) { rowEl.style.flexDirection = 'row'; }
            if (volEl) {
                var volHalf = volEl.closest('.chart-half');
                if (volHalf) volHalf.style.display = '';
                var volLabel = volEl.parentElement ? volEl.parentElement.querySelector('.chart-label') : null;
                if (volLabel) volLabel.textContent = 'HIGHEST VOLUME CONTRIBUTOR PER REGION';
            }
            if (cmEl) {
                var cmHalf = cmEl.closest('.chart-half');
                if (cmHalf) cmHalf.style.display = 'none';
            }
            if (volEl && sortedByVol.length) {
                renderSingleChartByAmount(volEl, 'ec' + region.charAt(0).toUpperCase() + region.slice(1) + 'Vol',
                    sortedByVol, 'volume', 'Volume', '', 'green');
            } else if (volEl) {
                volEl.innerHTML = '<div style="padding:2rem;text-align:center;color:#94a3b8;font-size:0.75rem;">No volume data.</div>';
            }
        } else if (category === 'margin') {
            // Show ONE chart - Contribution Margin (full width)
            if (rowEl) { rowEl.style.flexDirection = 'row'; }
            if (volEl) {
                var volHalf = volEl.closest('.chart-half');
                if (volHalf) volHalf.style.display = 'none';
            }
            if (cmEl) {
                var cmHalf = cmEl.closest('.chart-half');
                if (cmHalf) cmHalf.style.display = '';
                var cmLabel = cmEl.parentElement ? cmEl.parentElement.querySelector('.chart-label') : null;
                if (cmLabel) cmLabel.textContent = 'HIGHEST CONTRIBUTION MARGIN PER REGION';
            }
            if (cmEl && sortedByCm.length) {
                renderSingleChartByAmount(cmEl, 'ec' + region.charAt(0).toUpperCase() + region.slice(1) + 'Cm',
                    sortedByCm, 'grossAmount', 'Contribution Margin', '₱', 'red');
            } else if (cmEl) {
                cmEl.innerHTML = '<div style="padding:2rem;text-align:center;color:#94a3b8;font-size:0.75rem;">No CM data.</div>';
            }
        }
    }

    function renderSteelDeckCharts() {
        renderSteelDeckRegionCharts('south');
        renderSteelDeckRegionCharts('nc');
        renderSteelDeckRegionCharts('vis');
        renderSteelDeckRegionCharts('min');
    }

    // ===== CATEGORY-BASED DATA ENTRY =====
    function loadDashDeData() {
        var tab = currentTab;
        if (tab === 'primeBended') loadSeDeData(); else if (tab === 'primeSpandrel') loadTbDeData(); else loadEcDeData();
    }

    function loadSeDeData() {
        var tbody = document.getElementById('seDeBodyContent');
        if (!tbody) return;
        var cat = document.getElementById('seDeCategory') ? document.getElementById('seDeCategory').value : 'attainment';
        fetch('/data/se-list?category='+cat).then(function(r){return r.json();}).then(function(res){
            if (!res.success || !res.data || !res.data.length) {tbody.innerHTML = '<tr><td colspan="10" class="de-empty">No records.</td></tr>';return;}
            var html = '';
            res.data.forEach(function(r){
                html += '<tr><td>'+r.id+'</td><td>'+(r.region||'')+'</td><td>'+(r.name||'')+'</td><td>'+(r.area||'')+'</td><td>'+(r.position||'')+'</td>';
                if (cat==='attainment') {html+='<td>'+(parseFloat(r.attainment_percent)||0).toFixed(2)+'</td><td>'+Number(r.actual_volume||0).toLocaleString()+'</td><td>'+Number(r.budget||0).toLocaleString()+'</td>';}
                else {html+='<td>'+Number(r.revenue||0).toLocaleString()+'</td><td>'+Number(r.actual_cm||0).toLocaleString()+'</td><td>'+(parseFloat(r.price_lf)||0).toFixed(2)+'</td>';}
                html+='<td><button class="de-btn de-btn-edit" onclick="openDashModal(\'se\',\''+r.uuid+'\')" style="padding:0.15rem 0.35rem;font-size:0.55rem;">✎</button> <button class="de-btn de-btn-delete" onclick="deleteDashRecord(\'se\',\''+r.uuid+'\')" style="padding:0.15rem 0.35rem;font-size:0.55rem;">✕</button></td></tr>';
            });
            tbody.innerHTML = html;
        }).catch(function(err){tbody.innerHTML = '<tr><td colspan="10" class="de-empty">Error: '+err.message+'</td></tr>';});
    }

    function loadTbDeData() {
        var tbody = document.getElementById('tbDeBodyContent');
        if (!tbody) return;
        var cat = document.getElementById('tbDeCategory') ? document.getElementById('tbDeCategory').value : 'growth';
        fetch('/data/tb-list?category='+cat).then(function(r){return r.json();}).then(function(res){
            if (!res.success || !res.data || !res.data.length) {tbody.innerHTML='<tr><td colspan="10" class="de-empty">No records.</td></tr>';return;}
            var html='';
            res.data.forEach(function(r){
                html+='<tr><td>'+r.id+'</td><td>'+(r.region||'')+'</td><td>'+(r.sales_office||'')+'</td><td>'+(r.name||'')+'</td><td>'+(r.area||'')+'</td><td>'+(r.position||'')+'</td>';
                if(cat==='growth'){html+='<td>'+(parseFloat(r.growth_percent)||0).toFixed(2)+'</td><td>'+Number(r.last_month||0).toLocaleString()+'</td><td>'+Number(r.current_month||0).toLocaleString()+'</td>';}
                else{html+='<td>'+(parseFloat(r.attainment_percent)||0).toFixed(2)+'</td><td>'+Number(r.actual||0).toLocaleString()+'</td><td>'+Number(r.budget||0).toLocaleString()+'</td>';}
                html+='<td><button class="de-btn de-btn-edit" onclick="openDashModal(\'tb\',\''+r.uuid+'\')" style="padding:0.15rem 0.35rem;font-size:0.55rem;">✎</button> <button class="de-btn de-btn-delete" onclick="deleteDashRecord(\'tb\',\''+r.uuid+'\')" style="padding:0.15rem 0.35rem;font-size:0.55rem;">✕</button></td></tr>';
            });
            tbody.innerHTML=html;
        }).catch(function(err){tbody.innerHTML='<tr><td colspan="10" class="de-empty">Error: '+err.message+'</td></tr>';});
    }

    function loadEcDeData() {
        var tbody = document.getElementById('ecDeBodyContent');
        if (!tbody) return;
        fetch('/data/ec-list').then(function(r){return r.json();}).then(function(res){
            if (!res.success || !res.data || !res.data.length) {tbody.innerHTML='<tr><td colspan="8" class="de-empty">No records.</td></tr>';return;}
            var html='';
            res.data.forEach(function(r){
                html+='<tr><td>'+r.id+'</td><td>'+(r.quarter_year||'')+'</td><td>'+(r.region||'')+'</td><td>'+(r.top_volume_name||'')+'</td><td>'+(r.top_cm_name||'')+'</td><td>'+Number(r.total_volume||0).toLocaleString()+'</td><td>'+Number(r.total_cm||0).toLocaleString()+'</td>';
                html+='<td><button class="de-btn de-btn-edit" onclick="openDashModal(\'ec\',\''+r.uuid+'\')" style="padding:0.15rem 0.35rem;font-size:0.55rem;">✎</button> <button class="de-btn de-btn-delete" onclick="deleteDashRecord(\'ec\',\''+r.uuid+'\')" style="padding:0.15rem 0.35rem;font-size:0.55rem;">✕</button></td></tr>';
            });
            tbody.innerHTML=html;
        }).catch(function(err){tbody.innerHTML='<tr><td colspan="8" class="de-empty">Error: '+err.message+'</td></tr>';});
    }

    // ===== MODAL - Form builders =====
    window.openDashModal = function(type, editId, defaultRegion) {
        dashModalType = type;
        dashModalAction = editId ? 'edit' : 'create';
        dashModalEditId = editId;
        var title = document.getElementById('dashModalTitle');
        document.getElementById('dashModalBody').innerHTML = '';
        if (type === 'se') {
            title.textContent = editId ? 'Edit PRIME BENDED Record' : 'Add PRIME BENDED Record';
            // Detect current category from the table filter based on region
            dashModalCategory = 'attainment';
            if (defaultRegion) {
                var filterMap = {'SOUTH LUZON':'seSouthCat','NORTH & CENTRAL LUZON':'seNcCat','VISAYAS':'seVisCat','MINDANAO':'seMinCat'};
                var filterEl = document.getElementById(filterMap[defaultRegion]);
                if (filterEl) dashModalCategory = filterEl.value;
            }
            document.getElementById('dashModalBody').innerHTML = buildSeForm(defaultRegion, dashModalCategory);
        } else if (type === 'tb') {
            title.textContent = editId ? 'Edit TOP BRANCH RECOGNITION Record' : 'Add TOP BRANCH RECOGNITION Record';
            // Detect current category from the table filter based on region
            dashModalCategory = 'attainment';
            if (defaultRegion) {
                var filterMap = {'SOUTH LUZON':'tbSouthCat','NORTH & CENTRAL LUZON':'tbNcCat','VISAYAS':'tbVisCat','MINDANAO':'tbMinCat'};
                var filterEl = document.getElementById(filterMap[defaultRegion]);
                if (filterEl) dashModalCategory = filterEl.value;
            }
            document.getElementById('dashModalBody').innerHTML = buildTbForm(defaultRegion, dashModalCategory);
        } else if (type === 'ec') {
            title.textContent = editId ? 'Edit STEEL DECK Record' : 'Add STEEL DECK Record';
            // Detect current category from the table filter based on region
            dashModalCategory = 'volume';
            if (defaultRegion) {
                var filterMap = {'SOUTH LUZON':'ecSouthCat','NORTH & CENTRAL LUZON':'ecNcCat','VISAYAS':'ecVisCat','MINDANAO':'ecMinCat'};
                var filterEl = document.getElementById(filterMap[defaultRegion]);
                if (filterEl) dashModalCategory = filterEl.value;
            }
            document.getElementById('dashModalBody').innerHTML = buildSdForm(defaultRegion, dashModalCategory);
        }
        document.getElementById('dashModal').classList.add('show');
        if (editId) setTimeout(function(){ loadEditData(type, editId); }, 100);
    };

    window.closeDashModal = function() { document.getElementById('dashModal').classList.remove('show'); };
    function setVal(id, val) { var el = document.getElementById(id); if (el) el.value = val || ''; }
    function getVal(id) { var el = document.getElementById(id); return el ? el.value : ''; }

    function regionOpts(sel) {
        return dashRegions.map(function(r){return '<option value="'+r+'" '+(r===sel?'selected':'')+'>'+r+'</option>';}).join('');
    }

    // Photo field - photo is included with the record on Save (no separate upload button)
    function addPhotoUploadToForm(type) {
        var previewId = type + 'PhotoPreview';
        var inputId = type + 'PhotoInput';
        return '<div class="form-group" style="grid-column:1/-1;border-top:1px solid #e2e8f0;padding-top:8px;margin-top:4px;">' +
            '<label class="form-label">Participant Photo <span style="color:#dc2626;">*</span> <span style="color:#94a3b8;font-weight:400;font-size:0.6rem;">- saved together with the record</span></label>' +
            '<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">' +
            '<input type="file" id="' + inputId + '" accept="image/*" style="font-size:0.72rem;flex:1;" onchange="previewEntryPhoto(this,\'' + type + '\')">' +
            '</div>' +
            '<div id="' + previewId + '" style="margin-top:4px;display:none;"><img style="max-width:120px;max-height:80px;border-radius:4px;border:1px solid #e2e8f0;"></div>' +
            '<input type="hidden" id="' + type + 'Photo" value="">' +
            '</div>';
    }

    window.previewEntryPhoto = function(input, type) {
        var preview = document.getElementById(type + 'PhotoPreview');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.style.display = 'block';
                preview.querySelector('img').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    function buildSeForm(defaultRegion, category) {
        var cat = category || 'attainment';
        var attainmentDisplay = cat === 'attainment' ? '' : 'display:none;';
        var marginDisplay = cat === 'margin' ? '' : 'display:none;';
        return '<div class="form-grid">' +
            '<div class="form-group"><label class="form-label">Region</label><select class="form-control-sm" id="seRegion">'+regionOpts(defaultRegion)+'</select></div>' +
            '<div class="form-group"><label class="form-label">Name *</label><input class="form-control-sm" id="seName" placeholder="Full name"></div>' +
            '<div class="form-group"><label class="form-label">Area</label><input class="form-control-sm" id="seArea" placeholder="Area/Company"></div>' +
            '<div class="form-group"><label class="form-label">Position</label><input class="form-control-sm" id="sePosition" placeholder="Position"></div>' +
            // Attainment fields
            '<div class="form-group" style="'+attainmentDisplay+'"><label class="form-label">% Attainment</label><input class="form-control-sm" id="seAttainment" type="number" step="0.01" placeholder="0.00"></div>' +
            '<div class="form-group" style="'+attainmentDisplay+'"><label class="form-label">Actual Volume</label><input class="form-control-sm" id="seActualVol" type="number" step="0.01" placeholder="0.00"></div>' +
            '<div class="form-group" style="'+attainmentDisplay+'"><label class="form-label">Budget</label><input class="form-control-sm" id="seBudget" type="number" step="0.01" placeholder="0.00"></div>' +
            // Margin fields
            '<div class="form-group" style="'+marginDisplay+'"><label class="form-label">Revenue</label><input class="form-control-sm" id="seRevenue" type="number" step="0.01" placeholder="0.00"></div>' +
            '<div class="form-group" style="'+marginDisplay+'"><label class="form-label">Actual CM</label><input class="form-control-sm" id="seActualCm" type="number" step="0.01" placeholder="0.00"></div>' +
            '<div class="form-group" style="'+marginDisplay+'"><label class="form-label">Price/LF</label><input class="form-control-sm" id="sePriceLf" type="number" step="0.01" placeholder="0.00"></div>' +
            // Hidden fields
            '<div class="form-group" style="display:none;"><input class="form-control-sm" id="seGrowth" value="0"></div>' +
            '<div class="form-group" style="display:none;"><input class="form-control-sm" id="seMargin" value="0"></div>' +
            // Hidden UUID holder for photo upload
            '<div class="form-group" style="display:none;"><input class="form-control-sm" id="sePhotoUuid" value=""></div>' +
            // Photo upload
            addPhotoUploadToForm('se') +
            '</div>';
    }

    function buildTbForm(defaultRegion, category) {
        var cat = category || 'attainment';
        var attainmentDisplay = cat === 'attainment' ? '' : 'display:none;';
        var growthDisplay = cat === 'margin' ? '' : 'display:none;';
        return '<div class="form-grid">' +
            '<div class="form-group"><label class="form-label">Region</label><select class="form-control-sm" id="tbRegion">'+regionOpts(defaultRegion)+'</select></div>' +
            '<div class="form-group"><label class="form-label">Sales Office *</label><input class="form-control-sm" id="tbSalesOffice" placeholder="Sales office"></div>' +
            '<div class="form-group"><label class="form-label">Name *</label><input class="form-control-sm" id="tbName" placeholder="Full name"></div>' +
            '<div class="form-group"><label class="form-label">Area</label><input class="form-control-sm" id="tbArea" placeholder="Area"></div>' +
            '<div class="form-group"><label class="form-label">Position</label><input class="form-control-sm" id="tbPosition" placeholder="Position"></div>' +
            // Attainment fields
            '<div class="form-group" style="'+attainmentDisplay+'"><label class="form-label">% Attainment</label><input class="form-control-sm" id="tbAttainment" type="number" step="0.01" placeholder="0.00"></div>' +
            '<div class="form-group" style="'+attainmentDisplay+'"><label class="form-label">Actual</label><input class="form-control-sm" id="tbActual" type="number" step="0.01" placeholder="0.00"></div>' +
            '<div class="form-group" style="'+attainmentDisplay+'"><label class="form-label">Budget</label><input class="form-control-sm" id="tbBudget" type="number" step="0.01" placeholder="0.00"></div>' +
            // Growth fields
            '<div class="form-group" style="'+growthDisplay+'"><label class="form-label">Last Month</label><input class="form-control-sm" id="tbLastMonth" type="number" step="0.01" placeholder="0.00"></div>' +
            '<div class="form-group" style="'+growthDisplay+'"><label class="form-label">Current Month</label><input class="form-control-sm" id="tbCurrentMonth" type="number" step="0.01" placeholder="0.00"></div>' +
            '<div class="form-group" style="'+growthDisplay+'"><label class="form-label">% Growth</label><input class="form-control-sm" id="tbGrowth" type="number" step="0.01" placeholder="0.00"></div>' +
            // Hidden fields
            '<div class="form-group" style="display:none;"><input class="form-control-sm" id="tbRevenue" value="0"></div>' +
            // Hidden UUID holder for photo upload
            '<div class="form-group" style="display:none;"><input class="form-control-sm" id="tbPhotoUuid" value=""></div>' +
            // Photo upload
            addPhotoUploadToForm('tb') +
            '</div>';
    }

    function buildSdForm(defaultRegion, category) {
        var cat = category || 'volume';
        var volumeDisplay = cat === 'volume' ? '' : 'display:none;';
        var marginDisplay = cat === 'margin' ? '' : 'display:none;';
        return '<div class="form-grid">' +
            '<div class="form-group"><label class="form-label">Region</label><select class="form-control-sm" id="ecRegion">'+regionOpts(defaultRegion)+'</select></div>' +
            '<div class="form-group"><label class="form-label">Name *</label><input class="form-control-sm" id="ecName" placeholder="Full name"></div>' +
            '<div class="form-group"><label class="form-label">Area</label><input class="form-control-sm" id="ecArea" placeholder="Area/Company"></div>' +
            '<div class="form-group"><label class="form-label">Position</label><input class="form-control-sm" id="ecPosition" placeholder="Position"></div>' +
            // Volume fields
            '<div class="form-group" style="'+volumeDisplay+'"><label class="form-label">Actual Volume</label><input class="form-control-sm" id="ecVolume" type="number" step="0.01" placeholder="0.00"></div>' +
            // Margin fields
            '<div class="form-group" style="'+marginDisplay+'"><label class="form-label">Actual CM</label><input class="form-control-sm" id="ecGrossAmount" type="number" step="0.01" placeholder="0.00"></div>' +
            // Hidden fields
            '<div class="form-group" style="display:none;"><input class="form-control-sm" id="ecCompany" value=""></div>' +
            '<div class="form-group" style="display:none;"><input class="form-control-sm" id="ecQtyInvoice" value="0"></div>' +
            '<div class="form-group" style="display:none;"><input class="form-control-sm" id="ecRevenue" value="0"></div>' +
            // Hidden UUID holder for photo upload
            '<div class="form-group" style="display:none;"><input class="form-control-sm" id="ecPhotoUuid" value=""></div>' +
            // Photo upload
            addPhotoUploadToForm('ec') +
            '</div>';
    }

    function buildEcForm(defaultRegion) {
        return '<div class="form-grid">' +
            '<div class="form-group"><label class="form-label">Region</label><select class="form-control-sm" id="ecRegion">'+regionOpts(defaultRegion)+'</select></div>' +
            '<div class="form-group"><label class="form-label">Company Name</label><input class="form-control-sm" id="ecArea" placeholder="Company name"></div>' +
            '<div class="form-group"><label class="form-label">Name *</label><input class="form-control-sm" id="ecName" placeholder="Full name"></div>' +
            '<div class="form-group"><label class="form-label">Quantity Invoice</label><input class="form-control-sm" id="ecQtyInvoice" type="number" step="0.01"></div>' +
            '<div class="form-group"><label class="form-label">Gross Amount</label><input class="form-control-sm" id="ecGrossAmount" type="number" step="0.01"></div>' +
            '<div class="form-group"><label class="form-label">Volume</label><input class="form-control-sm" id="ecVolume" type="number" step="0.01"></div>' +
            '<div class="form-group"><label class="form-label">Revenue</label><input class="form-control-sm" id="ecRevenue" type="number" step="0.01"></div>' +
            '<div class="form-group"><label class="form-label">Position</label><input class="form-control-sm" id="ecPosition" placeholder="Position"></div></div>';
    }

    function loadEditData(type, uuid) {
        var endpoint = type==='se'?'/data/se-list':(type==='tb'?'/data/tb-list':(type==='sd'||type==='ec'?'/data/ec-data-list':'/data/ec-records-list'));
        fetch(endpoint).then(function(r){return r.json();}).then(function(res){
            if(!res.success||!res.data)return;
            var record=null;res.data.forEach(function(r){if(r.uuid===uuid)record=r;});if(!record)return;
            if(type==='se'){
                setVal('seRegion',record.region);setVal('seName',record.name);setVal('seArea',record.area||'');setVal('sePosition',record.position||'');
                setVal('seAttainment',record.attainment_percent);setVal('seActualVol',record.actual_volume);setVal('seBudget',record.budget);
                setVal('seRevenue',record.revenue);setVal('seActualCm',record.actual_cm);setVal('sePriceLf',record.price_lf);setVal('seMargin',record.margin);
            } else if(type==='tb'){
                setVal('tbRegion',record.region);setVal('tbSalesOffice',record.sales_office||'');setVal('tbName',record.name);
                setVal('tbArea',record.area||'');setVal('tbPosition',record.position||'');
                setVal('tbGrowth',record.growth_percent);setVal('tbAttainment',record.attainment_percent);setVal('tbActual',record.actual);
                setVal('tbBudget',record.budget);setVal('tbLastMonth',record.last_month);setVal('tbCurrentMonth',record.current_month);
                setVal('tbRevenue',record.revenue);
            } else if(type==='sd'){
                setVal('sdRegion',record.region);setVal('sdName',record.name);setVal('sdCompany',record.company||'');setVal('sdArea',record.area||'');setVal('sdPosition',record.position||'');
                setVal('sdQtyInvoice',record.quantity_invoice||0);setVal('sdGrossAmount',record.gross_amount||0);
                setVal('sdVolume',record.volume||0);setVal('sdRevenue',record.revenue||0);
            } else if(type==='ec'){
                setVal('ecRegion',record.region);setVal('ecName',record.name);setVal('ecArea',record.area||'');setVal('ecPosition',record.position||'');
                setVal('ecQtyInvoice',record.quantity_invoice||0);setVal('ecGrossAmount',record.gross_amount||0);
                setVal('ecVolume',record.volume||0);setVal('ecRevenue',record.revenue||0);
            }
        }).catch(function(err){console.error(err);});
    }

    window.saveDashRecord = function() {
        var type = dashModalType;
        var isEdit = dashModalAction==='edit';
        var id = dashModalEditId;
        var url='',method='';
        var formData = new FormData();

        // Include photo file if selected (saved together with the record)
        var photoInput = document.getElementById(type + 'PhotoInput');
        var photoFile = photoInput && photoInput.files.length > 0 ? photoInput.files[0] : null;
        if (!isEdit && !photoFile) {
            alert('Participant photo is required. Please select a photo before saving.');
            return;
        }
        if (photoFile) {
            formData.append('photo', photoFile);
        }

        if(type==='se'){
            var selectedRegion = getVal('seRegion');
            var cat = dashModalCategory || 'attainment';
            var mInput = document.getElementById('dashMonthInput');
            var yInput = document.getElementById('dashYearInput');
            var mVal = mInput ? parseInt(mInput.value) : (new Date().getMonth() + 1);
            var yVal = yInput ? parseInt(yInput.value) : new Date().getFullYear();
            formData.append('region', selectedRegion);
            formData.append('name', getVal('seName'));
            formData.append('area', getVal('seArea'));
            formData.append('position', getVal('sePosition'));
            formData.append('category', cat);
            formData.append('attainment_percent', 0);
            formData.append('actual_volume', 0);
            formData.append('budget', 0);
            formData.append('revenue', 0);
            formData.append('actual_cm', 0);
            formData.append('price_lf', 0);
            formData.append('margin', 0);
            formData.append('growth', parseFloat(getVal('seGrowth'))||0);
            formData.append('sales_month', mVal);
            formData.append('sales_year', yVal);
            if(cat==='attainment'){
                formData.append('attainment_percent', parseFloat(getVal('seAttainment'))||0);
                formData.append('actual_volume', parseFloat(getVal('seActualVol'))||0);
                formData.append('budget', parseFloat(getVal('seBudget'))||0);
            } else {
                formData.append('revenue', parseFloat(getVal('seRevenue'))||0);
                formData.append('actual_cm', parseFloat(getVal('seActualCm'))||0);
                formData.append('price_lf', parseFloat(getVal('sePriceLf'))||0);
                formData.append('margin', parseFloat(getVal('seMargin'))||0);
            }
            url=isEdit?'/data/se-update/'+id:'/data/se-create';method='POST';
        } else if(type==='tb'){
            var mInput = document.getElementById('dashMonthInput');
            var yInput = document.getElementById('dashYearInput');
            var mVal = mInput ? parseInt(mInput.value) : (new Date().getMonth() + 1);
            var yVal = yInput ? parseInt(yInput.value) : new Date().getFullYear();
            var tbCat = dashModalCategory || 'attainment';
            formData.append('region', getVal('tbRegion'));
            formData.append('sales_office', getVal('tbSalesOffice'));
            formData.append('name', getVal('tbName'));
            formData.append('area', getVal('tbArea'));
            formData.append('position', getVal('tbPosition'));
            formData.append('category', tbCat);
            formData.append('growth_percent', parseFloat(getVal('tbGrowth'))||0);
            formData.append('attainment_percent', parseFloat(getVal('tbAttainment'))||0);
            formData.append('actual', parseFloat(getVal('tbActual'))||0);
            formData.append('budget', parseFloat(getVal('tbBudget'))||0);
            formData.append('last_month', parseFloat(getVal('tbLastMonth'))||0);
            formData.append('current_month', parseFloat(getVal('tbCurrentMonth'))||0);
            formData.append('revenue', parseFloat(getVal('tbRevenue'))||0);
            formData.append('sales_month', mVal);
            formData.append('sales_year', yVal);
            url=isEdit?'/data/tb-update/'+id:'/data/tb-create';method='POST';
        } else if(type==='sd'){
            var mInput = document.getElementById('dashMonthInput');
            var yInput = document.getElementById('dashYearInput');
            var mVal = mInput ? parseInt(mInput.value) : (new Date().getMonth() + 1);
            var yVal = yInput ? parseInt(yInput.value) : new Date().getFullYear();
            formData.append('region', getVal('sdRegion'));
            formData.append('name', getVal('sdName'));
            formData.append('company', getVal('sdCompany'));
            formData.append('area', getVal('sdArea'));
            formData.append('position', getVal('sdPosition'));
            formData.append('quantity_invoice', parseFloat(getVal('sdQtyInvoice'))||0);
            formData.append('gross_amount', parseFloat(getVal('sdGrossAmount'))||0);
            formData.append('volume', parseFloat(getVal('sdVolume'))||0);
            formData.append('revenue', parseFloat(getVal('sdRevenue'))||0);
            formData.append('sales_month', mVal);
            formData.append('sales_year', yVal);
            url=isEdit?'/data/ec-data-update/'+id:'/data/ec-data-create';method='POST';
        } else if(type==='ec'){
            var mInput = document.getElementById('dashMonthInput');
            var yInput = document.getElementById('dashYearInput');
            var mVal = mInput ? parseInt(mInput.value) : (new Date().getMonth() + 1);
            var yVal = yInput ? parseInt(yInput.value) : new Date().getFullYear();
            var ecCat = dashModalCategory || 'volume';
            formData.append('region', getVal('ecRegion'));
            formData.append('name', getVal('ecName'));
            formData.append('company', getVal('ecCompany'));
            formData.append('area', getVal('ecArea'));
            formData.append('position', getVal('ecPosition'));
            formData.append('category', ecCat);
            formData.append('quantity_invoice', parseFloat(getVal('ecQtyInvoice'))||0);
            formData.append('gross_amount', parseFloat(getVal('ecGrossAmount'))||0);
            formData.append('volume', parseFloat(getVal('ecVolume'))||0);
            formData.append('revenue', parseFloat(getVal('ecRevenue'))||0);
            formData.append('sales_month', mVal);
            formData.append('sales_year', yVal);
            url=isEdit?'/data/ec-data-update/'+id:'/data/ec-data-create';method='POST';
        }
        if(!formData.get('name')){alert('Name is required!');return;}
        var btn=document.getElementById('dashModalSaveBtn');
        btn.disabled=true;btn.textContent='Saving...';
        fetch(url,{method:method,body:formData})
            .then(function(r){return r.json();})
            .then(function(res){
                if(res.success){
                    closeDashModal();loadDashDeData();fetchDashboardData();alert(res.message||'Saved with photo!');
                }
                else{alert('Error: '+(res.error||'Save failed'));}
            }).catch(function(err){alert('Error: '+err.message);})
            .finally(function(){btn.disabled=false;btn.textContent='Save';});
    };

    window.deleteDashRecord = function(type,uuid){
        if(!confirm('Delete this record?'))return;
        var url='';
        if(type==='se') url='/data/se-delete/'+uuid;
        else if(type==='tb') url='/data/tb-delete/'+uuid;
        else if(type==='sd'||type==='ec') url='/data/ec-data-delete/'+uuid;
        else url='/data/ec-records-delete/'+uuid;
        fetch(url,{method:'DELETE'}).then(function(r){return r.json();}).then(function(res){
            if(res.success){loadDashDeData();fetchDashboardData();alert('Deleted!');}
            else{alert('Error: '+(res.error||'Delete failed'));}
        }).catch(function(err){alert('Error: '+err.message);});
    };

    function fetchDashboardData() {
        var mInput = document.getElementById('dashMonthInput');
        var yInput = document.getElementById('dashYearInput');
        var m = mInput ? mInput.value : '';
        var y = yInput ? yInput.value : '';
        var url = '/data/dashboard';
        if (m && y) url += '?month=' + m + '&year=' + y;
        fetch(url).then(function(r){return r.json();}).then(function(j){
            if(j.success){
                window.dashboardData = {sales_excellence:j.sales_excellence,top_branch:j.top_branch,elite_circle:j.elite_circle,ec_records:j.ec_records||[],elite_circle_data:j.elite_circle_data||[]};
                window.dashboardKPIs=j.kpis;
                window.hasDashboardData=true;
                renderAll(chartsIdActive());
            }
        }).catch(function(e){console.error(e);});
    }

    function syncDashInputs() {
        var mInput = document.getElementById('dashMonthInput');
        var yInput = document.getElementById('dashYearInput');
        if (mInput && monthEl) mInput.value = monthEl.value;
        if (yInput && yearEl) yInput.value = yearEl.value;
    }

    function setupFilters() {
        if(sideRegion)sideRegion.onchange=function(){applyRegionFilter();};
        if(dashYear){dashYear.value=yearEl.value;dashYear.onchange=function(){yearEl.value=dashYear.value;syncDashInputs();fetchDashboardData();};}
        if(dashMonth){dashMonth.value=monthEl.value;dashMonth.onchange=function(){monthEl.value=dashMonth.value;syncDashInputs();fetchDashboardData();};}
        yearEl.onchange=function(){if(dashYear)dashYear.value=yearEl.value;syncDashInputs();fetchDashboardData();};
        monthEl.onchange=function(){if(dashMonth)dashMonth.value=monthEl.value;syncDashInputs();fetchDashboardData();};
    }

    document.getElementById('dashExportBtn')?.addEventListener('click', function() {
        var type = currentTab;
        var y = dashYear ? dashYear.value : (yearEl ? yearEl.value : '2026');
        var m = dashMonth ? dashMonth.value : (monthEl ? monthEl.value : '1');
        window.open('/data/export/' + type + '?year=' + y + '&month=' + m, '_blank');
    });

        document.getElementById('dashSaveBtn')?.addEventListener('click', function() {
            var monthInput = document.getElementById('dashMonthInput');
            var yearInput = document.getElementById('dashYearInput');
            var year = yearInput ? parseInt(yearInput.value) : 2026;
            var month = monthInput ? parseInt(monthInput.value) : 7;
            if (!month || month < 1 || month > 12) { alert('Please enter a valid Month (1-12).'); return; }
            if (!year || year < 2020 || year > 2099) { alert('Please enter a valid Year.'); return; }
            var btn = document.getElementById('dashSaveBtn');
            btn.textContent = '💾 Saving...'; btn.disabled = true;
            fetch('/leaderboard/api/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ year: year, month: month })
            }).then(function(r) { return r.json(); }).then(function(res) {
                if (res.success) {
                    alert('✅ All data saved to leaderboard! (SE, TB, EC) - ' + (res.inserted || 0) + ' new, ' + (res.updated || 0) + ' updated for Month ' + month + '/ Year ' + year);
                } else {
                    alert('❌ ' + (res.error || 'No data found for Month ' + month + ', Year ' + year + '.'));
                }
            }).catch(function(err) { alert('Error: ' + err.message); }).finally(function() {
                btn.textContent = '💾 Save'; btn.disabled = false; fetchDashboardData();
            });
        });

    async function fetchDataAndInit() {
        var mInput = document.getElementById('dashMonthInput');
        var yInput = document.getElementById('dashYearInput');
        var initM = mInput ? mInput.value : '1';
        var initY = yInput ? yInput.value : '2026';
        try{var r=await fetch('/data/dashboard?month=' + initM + '&year=' + initY);var j=await r.json();if(j.success){window.dashboardData={sales_excellence:j.sales_excellence,top_branch:j.top_branch,elite_circle:j.elite_circle,ec_records:j.ec_records||[],elite_circle_data:j.elite_circle_data||[]};window.dashboardKPIs=j.kpis;window.hasDashboardData=true;}}catch(e){}
        monthEl.value=initM;
        setupFilters();
        
        
        var initialTab = '<?= $activeTab ?? 'primeBended' ?>';
        switchTab(initialTab);
        updateViewToggle();updateTopbarTitle(initialTab);renderAll(chartsIdActive());loadDashDeData();

        document.getElementById('dashInsertNewBtn')?.addEventListener('click', function() {
            var tab = currentTab || 'primeBended';
            // Map frontend tab names to internal type identifiers for modal CRUD
            var tabTypeMap = {primeBended:'se', primeSpandrel:'tb', steelDeck:'ec'};
            var modalType = tabTypeMap[tab] || 'se';
            window.dashboardData = null;
            window.hasDashboardData = false;
            Object.keys(chartInstances).forEach(function(key) {
                if (chartInstances[key]) { chartInstances[key].destroy(); delete chartInstances[key]; }
            });
            ['seSouthBody','seNcBody','seVisBody','seMinBody','tbSouthBody','tbNcBody','tbVisBody','tbMinBody','ecSouthBody','ecNcBody','ecVisBody','ecMinBody'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) el.innerHTML = '<tr><td colspan="9" style="padding:2rem;text-align:center;color:#94a3b8;">Tables are empty. Start adding new records below.</td></tr>';
            });
            ['seTopRegion','seHighestVolume','seHighestRevenue','seMonthlySales','tbTopRegion','tbHighestVolume','tbHighestRevenue','tbMonthlySales','ecTopRegion','ecHighestVolume','ecHighestRevenue','ecMonthlySales'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) el.textContent = '-';
            });
            ['seSouthAttChart','seNcAttChart','seVisAttChart','seMinAttChart','seSouthRevChart','seNcRevChart','seVisRevChart','seMinRevChart','tbSouthAttChart','tbNcAttChart','tbVisAttChart','tbMinAttChart','tbSouthGrowthChart','tbNcGrowthChart','tbVisGrowthChart','tbMinGrowthChart','ecSouthVolChart','ecNcVolChart','ecVisVolChart','ecMinVolChart','ecSouthCmChart','ecNcCmChart','ecVisCmChart','ecMinCmChart'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) el.innerHTML = '';
            });
            openDashModal(modalType, null, null);
        });

        document.getElementById('dashRetrieveBtn')?.addEventListener('click', function() {
            var monthInput = document.getElementById('dashMonthInput');
            var yearInput = document.getElementById('dashYearInput');
            var month = monthInput ? parseInt(monthInput.value) : 4;
            var year = yearInput ? parseInt(yearInput.value) : 2026;
            if (!month || month < 1 || month > 12) { alert('Please enter a valid Month (1-12).'); return; }
            if (!year || year < 2020 || year > 2099) { alert('Please enter a valid Year.'); return; }
            var btn = document.getElementById('dashRetrieveBtn');
            btn.textContent = '⏳ Retrieving...'; btn.disabled = true;
            Object.keys(chartInstances).forEach(function(key) {
                if (chartInstances[key]) { chartInstances[key].destroy(); delete chartInstances[key]; }
            });
            if (sideRegion) sideRegion.value = '';
            fetch('/data/dashboard?month=' + month + '&year=' + year)
                .then(function(r){return r.json();}).then(function(j) {
                    if (j.success) {
                        var hasAnyData = (j.sales_excellence||[]).length > 0 || (j.top_branch||[]).length > 0 || (j.elite_circle||[]).length > 0 || (j.ec_records||[]).length > 0 || (j.elite_circle_data||[]).length > 0;
                        window.dashboardData = {sales_excellence:j.sales_excellence||[],top_branch:j.top_branch||[],elite_circle:j.elite_circle||[],ec_records:j.ec_records||[],elite_circle_data:j.elite_circle_data||[]};
                        window.dashboardKPIs = j.kpis;
                        window.hasDashboardData = hasAnyData;
                        if (yearEl) yearEl.value = year; if (monthEl) monthEl.value = month;
                        if (dashYear) dashYear.value = year; if (dashMonth) dashMonth.value = month;
                        renderAll(true);
                        alert(hasAnyData ? '✅ Data retrieved for ' + month + '/' + year + ' successfully!' : '❌ No data found for Month ' + month + ', Year ' + year + '.');
                    } else { alert('❌ No data found.'); }
                }).catch(function(err) { alert('Error: ' + err.message); })
                .finally(function() { btn.textContent = '📂 Retrieve Data'; btn.disabled = false; });
        });
    }
    if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',fetchDataAndInit);else fetchDataAndInit();
})();
</script>
<?= $this->endSection() ?>