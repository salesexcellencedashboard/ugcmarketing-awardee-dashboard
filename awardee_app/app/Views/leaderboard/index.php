<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<style>
.main-area { overflow: hidden !important; }
.topbar { display: none !important; }
html, body { height: 100%; }
.lb-header { background: #fff; color: #1e293b; padding: 0.85rem 1rem; border-radius: 12px; margin-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; border: 1px solid #e2e8f0; box-shadow: 0 1px 4px rgba(0,0,0,0.03); }
.lb-header h3 { margin: 0; font-size: 1.6rem; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; background: linear-gradient(135deg, #059669, #dc2626); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.lb-header .lb-subtitle { font-size: 0.82rem; opacity: 0.7; color: #64748b; margin-top: 4px; }

.lb-controls { display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; }
.lb-controls label { font-size: 0.65rem; font-weight: 700; color: #475569; text-transform: uppercase; }
.lb-controls select { font-size: 0.72rem; padding: 0.3rem 0.6rem; border-radius: 6px; border: 1.5px solid #d1d5db; background: #fff; color: #1e293b; cursor: pointer; font-weight: 600; }
.lb-controls select:focus { outline: none; border-color: #059669; box-shadow: 0 0 0 2px rgba(5,150,105,0.15); }
.lb-controls select option { color: #000; background: #fff; }

.lb-table-wrap { background: #fff; border-radius: 12px; border: 1px solid #eef0f4; box-shadow: 0 2px 8px rgba(0,0,0,0.04); overflow: hidden; display: flex; flex-direction: column; }
.lb-grid-2x2 { display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: 1fr 1fr; gap: 10px; flex: 1; min-height: 0; height: 100%; }
.lb-grid-2x2.filtered { grid-template-columns: 1fr; grid-template-rows: 1fr; }
.lb-grid-2x2.filtered .panel-card { grid-column: 1; grid-row: 1; }
.lb-grid-2x2 .panel-card { border-radius: 12px; border: 1px solid #eef0f4; display: flex; flex-direction: column; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.04); overflow: hidden; height: 100%; min-height: 0; }
.lb-grid-2x2 .panel-card.hidden { display: none; }
.lb-grid-2x2 .panel-header { background: #6b7280; color: #fff; padding: 0.5rem 0.75rem; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; flex-shrink: 0; }
.lb-grid-2x2 .panel-body { flex: 1; min-height: 0; padding: 0; display: flex; flex-direction: column; }
.lb-grid-2x2 .panel-body .lb-table { width: 100%; border-collapse: collapse; font-size: 0.7rem; }
.lb-grid-2x2 .panel-body .lb-table thead { position: sticky; top: 0; z-index: 2; }
.lb-grid-2x2 .panel-body .lb-table td, 
.lb-grid-2x2 .panel-body .lb-table th { padding: 0.35rem 0.25rem; font-size: 0.55rem; white-space: nowrap; }
.lb-grid-2x2 .panel-body .lb-table .lb-name-col { font-size: 0.55rem; min-width: 100px; }
.lb-grid-2x2 .panel-body .lb-table .lb-company-col { font-size: 0.55rem; min-width: 80px; }
.lb-grid-2x2 .panel-body .lb-table .lb-month-col { width: 30px; min-width: 28px; font-size: 0.52rem; }
.lb-grid-2x2 .panel-body .lb-table .lb-total-cell { font-size: 0.6rem; }
.lb-grid-2x2 .panel-body .lb-empty-small { padding: 0.5rem; text-align: center; color: #94a3b8; font-size: 0.65rem; }
.lb-grid-2x2 .panel-card.empty-card .panel-body { display: flex; align-items: center; justify-content: center; }
.lb-table-header { background: #374151; color: #fff; padding: 0.6rem 0.75rem; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; }
.lb-table-header .lb-table-title { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
.lb-table-header .lb-table-subtitle { font-size: 0.62rem; opacity: 0.8; }
.lb-table-scroll { overflow: auto; flex: 1; min-height: 0; max-height: 100%; }

.lb-table { width: 100%; border-collapse: collapse; font-size: 0.7rem; }
.lb-table thead { position: sticky; top: 0; z-index: 2; }
.lb-table th { padding: 0.45rem 0.3rem; text-align: center; border: 1px solid #d1d5db; font-size: 0.6rem; text-transform: uppercase; color: #374151; font-weight: 800; letter-spacing: 0.3px; background: #f1f5f9; white-space: nowrap; }
.lb-table td { padding: 0.4rem 0.3rem; border: 1px solid #e2e8f0; text-align: center; font-size: 0.62rem; color: #334155; vertical-align: middle; white-space: nowrap; }
.lb-table .lb-name-col { text-align: left; font-weight: 700; min-width: 120px; }
.lb-table .lb-company-col { text-align: left; font-weight: 600; min-width: 100px; }

.lb-empty { padding: 2rem; text-align: center; color: #94a3b8; font-size: 0.8rem; }

.lb-tabs { display: flex; gap: 2px; margin-bottom: 10px; flex-shrink: 0; }
.lb-tab { padding: 0.5rem 1rem; background: #e5e7eb; border: none; font-size: 0.72rem; font-weight: 700; cursor: pointer; border-radius: 8px 8px 0 0; color: #6b7280; transition: all 0.2s; }
.lb-tab.active { background: var(--ugc-red); color: #fff; }
.lb-tab:hover:not(.active) { background: #d1d5db; }

.lb-content { display: none; flex-direction: column; flex: 1; min-height: 0; max-height: 100%; height: 100%; }
.lb-content.active { display: flex; }
#lbSeRegionGrid, #lbTbRegionGrid, #lbEcRegionGrid { flex: 1; min-height: 0; display: flex; flex-direction: column; }

.lb-table .lb-month-col { min-width: 38px; font-size: 0.58rem; }

.lb-root { flex: 1; display: flex; flex-direction: column; min-height: 0; height: 100%; max-height: 100%; overflow: hidden; }

.de-btn { padding: 0.3rem 0.6rem; border: none; border-radius: 4px; font-size: 0.62rem; font-weight: 700; cursor: pointer; transition: all 0.15s ease; text-transform: uppercase; letter-spacing: 0.2px; display: inline-flex; align-items: center; gap: 3px; }
.de-btn-primary { background: #059669; color: #fff; }
.de-btn-primary:hover { background: #047857; }
.de-btn-export { background: #374151; color: #fff; }
.de-btn-export:hover { background: #1f2937; }

.lb-save-status { font-size: 0.7rem; padding: 4px 10px; border-radius: 6px; background: #f0fdf4; color: #059669; display: none; border: 1px solid #bbf7d0; }
.lb-save-status.error { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
.lb-save-status.show { display: inline-block; }

/* Rank styling */
.lb-rank-1 { background: linear-gradient(135deg,#fffbeb,#fef3c7); }
.lb-rank-2 { background: linear-gradient(135deg,#f8fafc,#e5e7eb); }
.lb-rank-3 { background: linear-gradient(135deg,#fff7ed,#fed7aa); }
.lb-total-cell { font-weight: 800; color: #059669; font-size: 0.7rem; }
</style>

<div class="lb-root">
<div class="lb-header">
    <div>
        <h3>LEADERBOARD DASHBOARD</h3>
        <div class="lb-subtitle">Monthly Rank Tracking Per Region</div>
    </div>
    <div class="lb-controls">
        <span style="font-size:0.65rem;font-weight:700;color:#475569;text-transform:uppercase;">Year:</span>
        <input type="number" id="lbYearFilter" min="2020" max="2099" value="<?= (int)date('Y') ?>" style="width:70px;font-size:0.72rem;padding:0.25rem 0.4rem;border:1.5px solid #d1d5db;border-radius:6px;text-align:center;">
        <span style="font-size:0.65rem;font-weight:700;color:#475569;text-transform:uppercase;margin-left:4px;">Category:</span>
        <select id="lbCategoryFilter" style="font-size:0.72rem;padding:0.3rem 0.6rem;border-radius:6px;border:1.5px solid #d1d5db;background:#fff;color:#1e293b;cursor:pointer;font-weight:600;min-width:200px;">
            <option value="attainment">HIGHEST % ATTAINMENT OVER BUDGET</option>
            <option value="margin">HIGHEST % CONTRIBUTION MARGIN</option>
        </select>
        <span class="lb-save-status" id="lbSaveStatus"></span>
    </div>
</div>

<div class="lb-tabs">
    <button class="lb-tab active" data-lbtab="se">SALES EXCELLENCE AWARDEE</button>
    <button class="lb-tab" data-lbtab="tb">TOP BRANCH RECOGNITION</button>
    <button class="lb-tab" data-lbtab="ec">SALES EXCELLENCE ELITE CIRCLE</button>
</div>

<!-- PRIME BENDED Leaderboard -->
<div id="lbSeContent" class="lb-content active">
    <div id="lbSeRegionGrid">
        <div class="lb-empty" style="text-align:center;padding:2rem;">Loading leaderboard data...</div>
    </div>
</div>

<!-- PRIME SPANDREL Leaderboard -->
<div id="lbTbContent" class="lb-content">
    <div id="lbTbRegionGrid">
        <div class="lb-empty" style="text-align:center;padding:2rem;">No data available.</div>
    </div>
</div>

<!-- STEEL DECK Leaderboard -->
<div id="lbEcContent" class="lb-content">
    <div id="lbEcRegionGrid">
        <div class="lb-empty" style="text-align:center;padding:2rem;">No data available.</div>
    </div>
</div>

</div>
<script>
(function() {
    var monthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var currentTab = 'se';
    var activeData = [];

    var yearFilter = document.getElementById('lbYearFilter');
    var categoryFilter = document.getElementById('lbCategoryFilter');
    var sideRegion = document.getElementById('sideRegionFilter');
    var regionNames = ['SOUTH LUZON', 'NORTH & CENTRAL LUZON', 'VISAYAS', 'MINDANAO'];

    function fmtRank(rank) {
        if (!rank || rank === 0) return '-';
        if (rank === 1) return '1st';
        if (rank === 2) return '2nd';
        if (rank === 3) return '3rd';
        return rank + 'th';
    }

    function getRankClass(rank) {
        if (rank === 1) return 'lb-rank-1';
        if (rank === 2) return 'lb-rank-2';
        if (rank === 3) return 'lb-rank-3';
        return '';
    }

    function makeRegionTable(items, hcols) {
        var wrapper = document.createElement('div');
        wrapper.style.cssText = 'width:100%;overflow:auto;flex:1;min-height:0;';
        // Force table to be wider than container to enable horizontal scroll for many columns
        var tbl = document.createElement('table');
        tbl.className = 'lb-table';
        tbl.style.cssText = 'border-collapse:collapse;font-size:0.7rem;min-width:650px;';
        var thead = document.createElement('thead');
        var hrow = document.createElement('tr');
        hcols.forEach(function(c) {
            var th = document.createElement('th');
            th.textContent = c;
            if (c === 'NAME') th.className = 'lb-name-col';
            if (c === 'COMPANY/AREA' || c === 'SALES OFFICE') th.className = 'lb-company-col';
            if (monthLabels.indexOf(c) !== -1) th.className = 'lb-month-col';
            hrow.appendChild(th);
        });
        thead.appendChild(hrow);
        tbl.appendChild(thead);
        var tbody = document.createElement('tbody');
        items.forEach(function(p, i) {
            var tr = document.createElement('tr');
            var rank = i + 1;
            tr.innerHTML += '<td>' + rank + '</td>';
            tr.innerHTML += '<td class="lb-name-col">' + (p.name || '—') + '</td>';
            tr.innerHTML += '<td class="lb-company-col">' + (p.area || p.sales_office || '—') + '</td>';
            var totalTop = 0;
            for (var m = 1; m <= 12; m++) {
                var r = 0;
                if (p.monthly && p.monthly[m]) {
                    r = typeof p.monthly[m] === 'object' ? (p.monthly[m].rank || 0) : p.monthly[m];
                }
                var cls = getRankClass(r);
                if (r >= 1 && r <= 3) totalTop++;
                tr.innerHTML += '<td class="' + cls + '">' + (r ? fmtRank(r) : '-') + '</td>';
            }
            tr.innerHTML += '<td class="lb-total-cell">' + totalTop + '</td>';
            tbody.appendChild(tr);
        });
        tbl.appendChild(tbody);
        wrapper.appendChild(tbl);
        return wrapper;
    }

    function buildLeaderboardCard(reg, items, hcols) {
        var card = document.createElement('div');
        card.className = 'panel-card';
        var hdr = document.createElement('div');
        hdr.className = 'panel-header';
        hdr.textContent = reg;
        card.appendChild(hdr);
        var body = document.createElement('div');
        body.className = 'panel-body';
        body.style.cssText = 'flex:1;min-height:0;display:flex;flex-direction:column;padding:0.35rem 0.5rem;overflow:hidden;';
        body.appendChild(makeRegionTable(items, hcols));
        card.appendChild(body);
        return card;
    }

    function renderGrid(container, data, hcols, emptyMsg) {
        container.innerHTML = '';
        if (!data) {
            container.innerHTML = '<div class="lb-empty" style="text-align:center;padding:2rem;">' + emptyMsg + '</div>';
            return;
        }
        var hasAny = false;
        regionNames.forEach(function(reg) {
            var items = data[reg] || [];
            if (items.length > 0) hasAny = true;
        });
        if (!hasAny) {
            container.innerHTML = '<div class="lb-empty" style="text-align:center;padding:2rem;">' + emptyMsg + '</div>';
            return;
        }
        var grid = document.createElement('div');
        grid.className = 'lb-grid-2x2';
        regionNames.forEach(function(reg) {
            var items = data[reg] || [];
            if (!items.length) {
                // Create empty card placeholder
                var card = document.createElement('div');
                card.className = 'panel-card';
                var hdr = document.createElement('div');
                hdr.className = 'panel-header';
                hdr.textContent = reg;
                card.appendChild(hdr);
                var body = document.createElement('div');
                body.className = 'panel-body';
                body.innerHTML = '<div class="lb-empty" style="padding:1rem;text-align:center;color:#94a3b8;font-size:0.7rem;">No data for this region.</div>';
                card.appendChild(body);
                grid.appendChild(card);
                return;
            }
            grid.appendChild(buildLeaderboardCard(reg, items, hcols));
        });
        container.appendChild(grid);
    }

    // ============================================================
    // PRIME BENDED - uses sales_excellence_leaderboard table
    // Format: Company Name (area) | Name | Jan | Feb | ... | Dec | TOTAL
    // ============================================================
    function renderSeLeaderboard(data) {
        var container = document.getElementById('lbSeRegionGrid');
        if (!container) return;
        if (!data || !Object.keys(data).length) {
            container.innerHTML = '<div class="lb-empty" style="text-align:center;padding:2rem;">No leaderboard data. Save data from dashboard first.</div>';
            return;
        }
        renderGrid(container, data, ['#','NAME','COMPANY/AREA'].concat(monthLabels).concat(['TOTAL']), 'No leaderboard data. Save data from dashboard first.');
    }

    // ============================================================
    // PRIME SPANDREL - uses top_branch_data / grandSlamTbData API
    // Format: Company Name (sales_office) | Name | Jan | Feb | ... | Dec | TOTAL
    // ============================================================
    function makeTbRegionTable(items, hcols) {
        var wrapper = document.createElement('div');
        wrapper.style.cssText = 'width:100%;overflow:auto;flex:1;min-height:0;';
        var tbl = document.createElement('table');
        tbl.className = 'lb-table';
        tbl.style.cssText = 'border-collapse:collapse;font-size:0.7rem;min-width:650px;';
        var thead = document.createElement('thead');
        var hrow = document.createElement('tr');
        hcols.forEach(function(c) {
            var th = document.createElement('th');
            th.textContent = c;
            if (c === 'NAME') th.className = 'lb-name-col';
            if (c === 'SALES OFFICE') th.className = 'lb-company-col';
            if (monthLabels.indexOf(c) !== -1) th.className = 'lb-month-col';
            hrow.appendChild(th);
        });
        thead.appendChild(hrow);
        tbl.appendChild(thead);
        var tbody = document.createElement('tbody');
        items.forEach(function(p, i) {
            var tr = document.createElement('tr');
            tr.innerHTML += '<td>' + (i + 1) + '</td>';
            tr.innerHTML += '<td class="lb-name-col">' + (p.name || '—') + '</td>';
            tr.innerHTML += '<td class="lb-company-col">' + (p.sales_office || p.area || '—') + '</td>';
            var dataCount = 0;
            for (var m = 1; m <= 12; m++) {
                var score = 0;
                if (p.monthly && p.monthly[m]) {
                    score = typeof p.monthly[m] === 'object' ? (p.monthly[m].score || 0) : p.monthly[m];
                }
                if (score > 0) dataCount++;
                tr.innerHTML += '<td>' + (score > 0 ? score.toFixed(2) + '%' : '-') + '</td>';
            }
            tr.innerHTML += '<td class="lb-total-cell">' + dataCount + '</td>';
            tbody.appendChild(tr);
        });
        tbl.appendChild(tbody);
        wrapper.appendChild(tbl);
        return wrapper;
    }

    function buildTbLeaderboardCard(reg, items, hcols) {
        var card = document.createElement('div');
        card.className = 'panel-card';
        var hdr = document.createElement('div');
        hdr.className = 'panel-header';
        hdr.textContent = reg;
        card.appendChild(hdr);
        var body = document.createElement('div');
        body.className = 'panel-body';
        body.style.cssText = 'flex:1;min-height:0;display:flex;flex-direction:column;padding:0.35rem 0.5rem;overflow:hidden;';
        body.appendChild(makeTbRegionTable(items, hcols));
        card.appendChild(body);
        return card;
    }

    function renderTbLeaderboard(data) {
        var container = document.getElementById('lbTbRegionGrid');
        if (!container) return;
        container.innerHTML = '';
        if (!data || !data.length) {
            container.innerHTML = '<div class="lb-empty" style="text-align:center;padding:2rem;">No data available.</div>';
            return;
        }
        var g = {};
        regionNames.forEach(function(r) { g[r] = []; });
        data.forEach(function(p) {
            var reg = p.region || 'SOUTH LUZON';
            if (g[reg]) g[reg].push(p);
        });
        // Use custom TB rendering that shows actual score values, not ranks
        var hasAny = false;
        regionNames.forEach(function(reg) {
            var items = g[reg] || [];
            if (items.length > 0) hasAny = true;
        });
        if (!hasAny) {
            container.innerHTML = '<div class="lb-empty" style="text-align:center;padding:2rem;">No data available.</div>';
            return;
        }
        var grid = document.createElement('div');
        grid.className = 'lb-grid-2x2';
        var hcols = ['#','NAME','SALES OFFICE'].concat(monthLabels).concat(['TOTAL']);
        regionNames.forEach(function(reg) {
            var items = g[reg] || [];
            if (!items.length) {
                var card = document.createElement('div');
                card.className = 'panel-card';
                var hdr = document.createElement('div');
                hdr.className = 'panel-header';
                hdr.textContent = reg;
                card.appendChild(hdr);
                var body = document.createElement('div');
                body.className = 'panel-body';
                body.innerHTML = '<div class="lb-empty" style="padding:1rem;text-align:center;color:#94a3b8;font-size:0.7rem;">No data for this region.</div>';
                card.appendChild(body);
                grid.appendChild(card);
                return;
            }
            grid.appendChild(buildTbLeaderboardCard(reg, items, hcols));
        });
        container.appendChild(grid);
    }

    // ============================================================
    // STEEL DECK - uses elite_circle_records / eliteCircleMonthlyRanking API
    // Format: Company Name (area) | Name | Jan | Feb | ... | Dec | TOTAL
    // ============================================================
    function renderEcLeaderboard(data) {
        var container = document.getElementById('lbEcRegionGrid');
        if (!container) return;
        if (!data || !data.length) {
            container.innerHTML = '<div class="lb-empty" style="text-align:center;padding:2rem;">No data.</div>';
            return;
        }
        var g = {};
        regionNames.forEach(function(r) { g[r] = []; });
        data.forEach(function(p) {
            var reg = p.region || 'SOUTH LUZON';
            if (g[reg]) g[reg].push(p);
        });
        renderGrid(container, g, ['#','NAME','COMPANY/AREA'].concat(monthLabels).concat(['TOTAL']), 'No data.');
    }

    function fetchLeaderboardData() {
        var year = yearFilter ? yearFilter.value : new Date().getFullYear();
        var cat = categoryFilter ? categoryFilter.value : 'attainment';

        if (currentTab === 'se') {
            fetch('/leaderboard/api/leaderboard-data?year=' + year + '&category=' + cat)
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.success && res.data) {
                        renderSeLeaderboard(res.data);
                    } else {
                        document.getElementById('lbSeRegionGrid').innerHTML = '<div class="lb-empty" style="text-align:center;padding:2rem;">No leaderboard data. Save data from dashboard first.</div>';
                    }
                })
                .catch(function(err) { console.error(err); });
        } else if (currentTab === 'tb') {
            fetch('/leaderboard/api/tb?year=' + year + '&category=' + encodeURIComponent(cat))
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.success && res.data) {
                        renderTbLeaderboard(res.data);
                    } else {
                        document.getElementById('lbTbRegionGrid').innerHTML = '<div class="lb-empty" style="text-align:center;padding:2rem;">No data available.</div>';
                    }
                })
                .catch(function(err) { console.error(err); });
        } else if (currentTab === 'ec') {
            fetch('/leaderboard/api/ec-monthly?year=' + year + '&category=' + cat)
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.success && res.data) {
                        renderEcLeaderboard(res.data);
                    } else {
                        document.getElementById('lbEcRegionGrid').innerHTML = '<div class="lb-empty" style="text-align:center;padding:2rem;">No data.</div>';
                    }
                })
                .catch(function(err) { console.error(err); });
        }
    }

    // Setup sidebar region filter (like dashboard - uses filtered class on grid)
    if (sideRegion) {
        sideRegion.addEventListener('change', function() {
            var region = sideRegion.value;
            // Get all active tab grid containers
            var containers = ['#lbSeRegionGrid', '#lbTbRegionGrid', '#lbEcRegionGrid'];
            containers.forEach(function(sel) {
                var grid = document.querySelector(sel + ' .lb-grid-2x2');
                if (!grid) return;
                if (region) {
                    grid.classList.add('filtered');
                    grid.querySelectorAll('.panel-card').forEach(function(card) {
                        var hdr = card.querySelector('.panel-header');
                        if (hdr) {
                            var title = hdr.textContent.trim();
                            card.classList.toggle('hidden', title !== region);
                        }
                    });
                } else {
                    grid.classList.remove('filtered');
                    grid.querySelectorAll('.panel-card').forEach(function(card) {
                        card.classList.remove('hidden');
                    });
                }
            });
        });
    }

    var tabCategories = {
        'se': {
            label: 'Category:',
            options: [
                { value: 'attainment', text: 'HIGHEST % ATTAINMENT OVER BUDGET' },
                { value: 'margin', text: 'HIGHEST % CONTRIBUTION MARGIN' }
            ]
        },
        'tb': {
            label: 'Category:',
            options: [
                { value: 'attainment', text: 'HIGHEST % OVERALL ATTAINMENT VS BUDGET' },
                { value: 'margin', text: 'HIGHEST % GROWTH VS LM' }
            ]
        },
        'ec': {
            label: 'Category:',
            options: [
                { value: 'volume_contributor', text: 'HIGHEST VOLUME CONTRIBUTOR PER REGION' },
                { value: 'cm_per_region', text: 'HIGHEST CONTRIBUTION MARGIN PER REGION' }
            ]
        }
    };

    function updateCategoryFilter(tab) {
        var config = tabCategories[tab];
        if (!config) return;
        var label = categoryFilter.parentElement.querySelector('span');
        if (label) label.textContent = config.label;
        var currentValue = categoryFilter.value;
        categoryFilter.innerHTML = '';
        config.options.forEach(function(opt) {
            var option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.text;
            categoryFilter.appendChild(option);
        });
        if (config.options.some(function(o) { return o.value === currentValue; })) {
            categoryFilter.value = currentValue;
        }
    }

    document.querySelectorAll('.lb-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.lb-tab').forEach(function(t) { t.classList.remove('active'); });
            tab.classList.add('active');
            currentTab = tab.getAttribute('data-lbtab');
            document.querySelectorAll('.lb-content').forEach(function(c) { c.classList.remove('active'); });
            document.getElementById('lb' + currentTab.charAt(0).toUpperCase() + currentTab.slice(1) + 'Content').classList.add('active');
            updateCategoryFilter(currentTab);
            fetchLeaderboardData();
        });
    });

    if (yearFilter) {
        yearFilter.addEventListener('change', function() {
            var y = parseInt(this.value);
            if (isNaN(y) || y < 2020) this.value = 2025;
            if (y > 2099) this.value = 2099;
            fetchLeaderboardData();
        });
    }

    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            fetchLeaderboardData();
        });
    }
    
    // Initialize category filter for default active tab
    updateCategoryFilter(currentTab);
    fetchLeaderboardData();
})();
</script>
<?= $this->endSection() ?>