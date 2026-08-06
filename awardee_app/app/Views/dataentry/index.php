<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.data-entry-container { display:flex; flex-direction:column; flex:1; min-height:0; }
.de-tab-bar { display:flex; gap:6px; margin-bottom:10px; flex-shrink:0; }
.de-tab { padding:0.55rem 1.2rem; border:1.5px solid #d1d5db; background:#fff; color:#6b7280; font-weight:700; font-size:0.75rem; cursor:pointer; border-radius:8px 8px 0 0; transition:all 0.2s ease; text-transform:uppercase; letter-spacing:0.3px; }
.de-tab:hover { background:#f3f4f6; border-color:#9ca3af; }
.de-tab.active { background:#374151; color:#fff; border-color:#374151; }
.de-panel { display:none; flex-direction:column; flex:1; min-height:0; background:#fff; border-radius:0 8px 8px 8px; border:1px solid #e5e7eb; overflow:hidden; }
.de-panel.active { display:flex; }
.de-toolbar { display:flex; justify-content:space-between; align-items:center; padding:0.6rem 0.8rem; background:#f8fafc; border-bottom:2px solid #374151; flex-shrink:0; flex-wrap:wrap; gap:6px; }
.de-toolbar-title { font-size:0.78rem; font-weight:800; color:#374151; text-transform:uppercase; letter-spacing:0.4px; }
.de-btn { padding:0.35rem 0.75rem; border:none; border-radius:5px; font-size:0.7rem; font-weight:700; cursor:pointer; transition:all 0.15s ease; text-transform:uppercase; letter-spacing:0.3px; display:inline-flex; align-items:center; gap:4px; }
.de-btn-primary { background:#059669; color:#fff; }
.de-btn-primary:hover { background:#047857; }
.de-btn-edit { background:#3b82f6; color:#fff; }
.de-btn-edit:hover { background:#2563eb; }
.de-btn-delete { background:#ef4444; color:#fff; }
.de-btn-delete:hover { background:#dc2626; }
.de-btn-secondary { background:#6b7280; color:#fff; }
.de-btn-secondary:hover { background:#4b5563; }
.de-btn-sm { padding:0.2rem 0.45rem; font-size:0.62rem; }
.de-table-wrap { flex:1; overflow:auto; padding:0; }
.de-table { width:100%; border-collapse:collapse; font-size:0.68rem; }
.de-table th { padding:0.45rem 0.4rem; text-align:center; border:1px solid #d1d5db; font-size:0.6rem; text-transform:uppercase; color:#475569; font-weight:800; letter-spacing:0.3px; background:#f1f5f9; white-space:nowrap; position:sticky; top:0; z-index:1; }
.de-table td { padding:0.4rem 0.35rem; border:1px solid #e2e8f0; text-align:center; font-size:0.65rem; color:#334155; vertical-align:middle; white-space:nowrap; }
.de-table tr:hover { background:#f8fafc; }
.de-table .actions-col { min-width:80px; }
.de-empty { padding:3rem; text-align:center; color:#94a3b8; font-size:0.85rem; }
.de-modal-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:99999; justify-content:center; align-items:center; padding:20px; }
.de-modal-overlay.show { display:flex; }
.de-modal { background:#fff; border-radius:12px; width:90%; max-width:900px; max-height:90vh; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,0.3); animation:modalFadeIn 0.2s ease; }
@keyframes modalFadeIn { from{opacity:0;transform:scale(0.95) translateY(10px)} to{opacity:1;transform:scale(1) translateY(0)} }
.de-modal-header { display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:#374151; color:#fff; flex-shrink:0; }
.de-modal-header h5 { margin:0; font-size:0.85rem; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; }
.de-modal-close { background:none; border:none; color:#fff; font-size:1.5rem; cursor:pointer; line-height:1; padding:0 4px; }
.de-modal-body { flex:1; overflow:auto; padding:16px; }
.de-modal-footer { padding:12px 16px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:8px; flex-shrink:0; }
.form-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; }
.form-grid .form-group { margin-bottom:0; }
.form-label { font-size:0.68rem; font-weight:700; color:#374151; margin-bottom:3px; display:block; text-transform:uppercase; letter-spacing:0.2px; }
.form-control-sm { font-size:0.75rem; padding:0.3rem 0.5rem; border:1px solid #d1d5db; border-radius:5px; width:100%; box-sizing:border-box; }
.form-control-sm:focus { outline:none; border-color:#059669; box-shadow:0 0 0 2px rgba(5,150,105,0.1); }
select.form-control-sm { cursor:pointer; }
/* Photo field styles */
.photo-field-section { margin-top:14px; padding:12px 14px; background:#f8fafc; border:1.5px dashed #d1d5db; border-radius:8px; }
.photo-field-section label { font-size:0.68rem; font-weight:700; color:#374151; margin-bottom:4px; display:block; text-transform:uppercase; letter-spacing:0.2px; }
.photo-field-section .photo-preview { width:70px; height:70px; border-radius:8px; overflow:hidden; border:2px solid #e2e8f0; background:#fff; display:flex; align-items:center; justify-content:center; font-size:1.5rem; color:#94a3b8; margin-bottom:8px; flex-shrink:0; }
.photo-field-section .photo-preview img { width:100%; height:100%; object-fit:cover; }
.photo-field-section .photo-input-row { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.photo-field-section .photo-hint { font-size:0.65rem; color:#6b7280; margin-top:4px; }
.photo-field-section .photo-status { font-size:0.68rem; margin-top:4px; }
@media (max-width:768px){.form-grid{grid-template-columns:1fr 1fr}}
@media (max-width:480px){.form-grid{grid-template-columns:1fr}}
</style>

<div class="data-entry-container">
    <div class="de-tab-bar">
        <button class="de-tab active" data-tab="seTab" onclick="switchDataTab('se')">Sales Excellence</button>
        <button class="de-tab" data-tab="tbTab" onclick="switchDataTab('tb')">Top Branch Recognition</button>
        <button class="de-tab" data-tab="ecTab" onclick="switchDataTab('ec')">Elite Circle</button>
    </div>

    <div class="de-panel active" id="sePanel">
        <div class="de-toolbar">
            <span class="de-toolbar-title">📊 Sales Excellence Awardee Data</span>
            <div style="display:flex;gap:4px;align-items:center;">
                <span style="font-size:0.68rem;font-weight:600;color:#374151;">Category:</span>
                <select id="seDeCategory" class="form-control-sm" style="width:120px;font-size:0.7rem;" onchange="seDeFilterChange()">
                    <option value="attainment">Attainment</option>
                    <option value="margin">Margin</option>
                </select>
                <button class="de-btn de-btn-primary" onclick="openModal('se',null)">+ Add Record</button>
                <button class="de-btn de-btn-secondary" onclick="refreshData('se')">↻ Refresh</button>
                <button class="de-btn de-btn-delete" onclick="clearDataType('se')" style="font-size:0.6rem;">🗑 Clear All SE</button>
            </div>
        </div>
        <div class="de-table-wrap">
            <table class="de-table" id="seTable">
                <thead id="seTableHead">
                    <tr><th>#</th><th>Region</th><th>Name</th><th>Area</th><th>Position</th><th>% Attain</th><th>Actual Vol</th><th>Budget</th><th class="actions-col">Actions</th></tr>
                </thead>
                <tbody id="seBody"><tr><td colspan="9" class="de-empty">Loading data...</td></tr></tbody>
            </table>
        </div>
    </div>

    <div class="de-panel" id="tbPanel">
        <div class="de-toolbar">
            <span class="de-toolbar-title">🏆 Top Branch Recognition Data</span>
            <div style="display:flex;gap:4px;align-items:center;">
                <span style="font-size:0.68rem;font-weight:600;color:#374151;">Category:</span>
                <select id="tbDeCategory" class="form-control-sm" style="width:120px;font-size:0.7rem;" onchange="tbDeFilterChange()">
                    <option value="growth">Growth</option>
                    <option value="attainment">Attainment</option>
                    <option value="margin">Margin</option>
                </select>
                <button class="de-btn de-btn-primary" onclick="openModal('tb',null)">+ Add Record</button>
                <button class="de-btn de-btn-secondary" onclick="refreshData('tb')">↻ Refresh</button>
                <button class="de-btn de-btn-delete" onclick="clearDataType('tb')" style="font-size:0.6rem;">🗑 Clear All TB</button>
            </div>
        </div>
        <div class="de-table-wrap">
            <table class="de-table" id="tbTable">
                <thead id="tbTableHead">
                    <tr><th>#</th><th>Region</th><th>Sales Office</th><th>Name</th><th>Area</th><th>Position</th><th>Growth %</th><th>Last Month</th><th>Current Month</th><th class="actions-col">Actions</th></tr>
                </thead>
                <tbody id="tbBody"><tr><td colspan="10" class="de-empty">Loading data...</td></tr></tbody>
            </table>
        </div>
    </div>

    <div class="de-panel" id="ecPanel">
        <div class="de-toolbar">
            <span class="de-toolbar-title">👑 Elite Circle Summary Data</span>
            <div>
                <button class="de-btn de-btn-primary" onclick="openModal('ec',null)">+ Add Record</button>
                <button class="de-btn de-btn-secondary" onclick="refreshData('ec')">↻ Refresh</button>
                <button class="de-btn de-btn-delete" onclick="clearDataType('ec')" style="font-size:0.6rem;">🗑 Clear All EC</button>
                <button class="de-btn de-btn-delete" onclick="clearAllData()" style="font-size:0.6rem;background:#dc2626;">⚠ Clear ALL System Data</button>
            </div>
        </div>
        <div class="de-table-wrap">
            <table class="de-table" id="ecTable">
                <thead><tr>
                    <th>#</th><th>Quarter/Year</th><th>Region</th><th>Top Vol Name</th><th>Top Vol Area</th><th>Top Vol Position</th><th>Top Vol Value</th><th>Top CM Name</th><th>Top CM Area</th><th>Top CM Position</th><th>Top CM Value</th><th>Total Volume</th><th>Total CM</th><th class="actions-col">Actions</th>
                </tr></thead>
                <tbody id="ecBody"><tr><td colspan="14" class="de-empty">Loading data...</td></tr></tbody>
            </table>
        </div>
    </div>
</div>

<div class="de-modal-overlay" id="dataModal">
    <div class="de-modal">
        <div class="de-modal-header">
            <h5 id="modalTitle">Add Record</h5>
            <button class="de-modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="de-modal-body" id="modalBody"></div>
        <div class="de-modal-footer">
            <button class="de-btn de-btn-secondary" onclick="closeModal()">Cancel</button>
            <button class="de-btn de-btn-primary" id="modalSaveBtn" onclick="saveRecord()">Save Record</button>
        </div>
    </div>
</div>

<script>
var currentTab = 'se';
var currentAction = 'create';
var currentEditId = null;
var currentPhotoUuid = null;
var currentPhotoType = null;
var regions = ['SOUTH LUZON', 'NORTH & CENTRAL LUZON', 'VISAYAS', 'MINDANAO'];

function seDeFilterChange() {
    var cat = document.getElementById('seDeCategory').value;
    var head = document.getElementById('seTableHead');
    if (cat === 'attainment') {
        head.innerHTML = '<tr><th>#</th><th>Region</th><th>Name</th><th>Area</th><th>Position</th><th>% Attain</th><th>Actual Vol</th><th>Budget</th><th class="actions-col">Actions</th></tr>';
    } else {
        head.innerHTML = '<tr><th>#</th><th>Region</th><th>Name</th><th>Area</th><th>Position</th><th>Revenue</th><th>Actual CM</th><th>Price/LF</th><th class="actions-col">Actions</th></tr>';
    }
    loadSEData();
}
function tbDeFilterChange() {
    var cat = document.getElementById('tbDeCategory').value;
    var head = document.getElementById('tbTableHead');
    if (cat === 'growth' || cat === 'margin') {
        head.innerHTML = '<tr><th>#</th><th>Region</th><th>Sales Office</th><th>Name</th><th>Area</th><th>Position</th><th>Growth %</th><th>Last Month</th><th>Current Month</th><th class="actions-col">Actions</th></tr>';
    } else {
        head.innerHTML = '<tr><th>#</th><th>Region</th><th>Sales Office</th><th>Name</th><th>Area</th><th>Position</th><th>% Attainment</th><th>Actual</th><th>Budget</th><th class="actions-col">Actions</th></tr>';
    }
    loadTBData();
}

function switchDataTab(tab) {
    currentTab = tab;
    document.querySelectorAll('.de-tab').forEach(function(el){el.classList.toggle('active',el.dataset.tab===tab+'Tab');});
    document.querySelectorAll('.de-panel').forEach(function(el){el.classList.toggle('active',el.id===tab+'Panel');});
    refreshData(tab);
}
function refreshData(tab) {
    if(tab==='se')loadSEData(); else if(tab==='tb')loadTBData(); else loadECData();
}

function loadSEData() {
    var tbody = document.getElementById('seBody');
    tbody.innerHTML = '<tr><td colspan="10" class="de-empty">Loading data...</td></tr>';
    var cat = document.getElementById('seDeCategory') ? document.getElementById('seDeCategory').value : 'attainment';
    fetch('/data/se-list?category='+cat+'&_='+Date.now()).then(function(r){return r.json();}).then(function(res){
        if(!res.success||!res.data||!res.data.length){tbody.innerHTML='<tr><td colspan="10" class="de-empty">No records found.</td></tr>';return;}
        var html='';
        res.data.forEach(function(r,i){
            html+='<tr><td>'+(i+1)+'</td><td>'+(r.region||'')+'</td><td>'+(r.name||'')+'</td><td>'+(r.area||'')+'</td><td>'+(r.position||'')+'</td>';
            if(cat==='attainment'){
                html+='<td>'+(parseFloat(r.attainment_percent)||0).toFixed(2)+'</td><td>'+Number(r.actual_volume||0).toLocaleString()+'</td><td>'+Number(r.budget||0).toLocaleString()+'</td>';
            } else {
                html+='<td>'+Number(r.revenue||0).toLocaleString()+'</td><td>'+Number(r.actual_cm||0).toLocaleString()+'</td><td>'+(parseFloat(r.price_lf)||0).toFixed(2)+'</td>';
            }
            html+='<td class="actions-col"><button class="de-btn de-btn-edit de-btn-sm" onclick="editRecord(\'se\',\''+r.uuid+'\')">✎ Edit</button> <button class="de-btn de-btn-delete de-btn-sm" onclick="deleteRecord(\'se\',\''+r.uuid+'\')">✕ Del</button></td></tr>';
        });
        tbody.innerHTML=html;
    }).catch(function(err){tbody.innerHTML='<tr><td colspan="10" class="de-empty">Error: '+err.message+'</td></tr>';});
}

function loadTBData() {
    var tbody = document.getElementById('tbBody');
    tbody.innerHTML = '<tr><td colspan="10" class="de-empty">Loading data...</td></tr>';
    var cat = document.getElementById('tbDeCategory') ? document.getElementById('tbDeCategory').value : 'growth';
    fetch('/data/tb-list?category='+cat+'&_='+Date.now()).then(function(r){return r.json();}).then(function(res){
        if(!res.success||!res.data||!res.data.length){tbody.innerHTML='<tr><td colspan="10" class="de-empty">No records found.</td></tr>';return;}
        var html='';
        res.data.forEach(function(r,i){
            html+='<tr><td>'+(i+1)+'</td><td>'+(r.region||'')+'</td><td>'+(r.sales_office||'')+'</td><td>'+(r.name||'')+'</td><td>'+(r.area||'')+'</td><td>'+(r.position||'')+'</td>';
            if(cat==='growth' || cat==='margin'){
                html+='<td>'+(parseFloat(r.growth_percent)||0).toFixed(2)+'</td><td>'+Number(r.last_month||0).toLocaleString()+'</td><td>'+Number(r.current_month||0).toLocaleString()+'</td>';
            } else {
                html+='<td>'+(parseFloat(r.attainment_percent)||0).toFixed(2)+'</td><td>'+Number(r.actual||0).toLocaleString()+'</td><td>'+Number(r.budget||0).toLocaleString()+'</td>';
            }
            html+='<td class="actions-col"><button class="de-btn de-btn-edit de-btn-sm" onclick="editRecord(\'tb\',\''+r.uuid+'\')">✎ Edit</button> <button class="de-btn de-btn-delete de-btn-sm" onclick="deleteRecord(\'tb\',\''+r.uuid+'\')">✕ Del</button></td></tr>';
        });
        tbody.innerHTML=html;
    }).catch(function(err){tbody.innerHTML='<tr><td colspan="10" class="de-empty">Error: '+err.message+'</td></tr>';});
}

function loadECData() {
    var tbody = document.getElementById('ecBody');
    tbody.innerHTML='<tr><td colspan="14" class="de-empty">Loading data...</td></tr>';
    fetch('/data/ec-list?_='+Date.now()).then(function(r){return r.json();}).then(function(res){
        if(!res.success||!res.data||!res.data.length){tbody.innerHTML='<tr><td colspan="14" class="de-empty">No records found.</td></tr>';return;}
        var html='';
        res.data.forEach(function(r,i){
            html+='<tr><td>'+(i+1)+'</td><td>'+(r.quarter_year||'')+'</td><td>'+(r.region||'')+'</td><td>'+(r.top_volume_name||'')+'</td><td>'+(r.top_volume_area||'')+'</td><td>'+(r.top_volume_position||'')+'</td>';
            html+='<td>'+Number(r.top_volume_value||0).toLocaleString()+'</td><td>'+(r.top_cm_name||'')+'</td><td>'+(r.top_cm_area||'')+'</td><td>'+(r.top_cm_position||'')+'</td>';
            html+='<td>'+Number(r.top_cm_value||0).toLocaleString()+'</td><td>'+Number(r.total_volume||0).toLocaleString()+'</td><td>'+Number(r.total_cm||0).toLocaleString()+'</td>';
            html+='<td class="actions-col"><button class="de-btn de-btn-edit de-btn-sm" onclick="editRecord(\'ec\',\''+r.uuid+'\')">✎ Edit</button> <button class="de-btn de-btn-delete de-btn-sm" onclick="deleteRecord(\'ec\',\''+r.uuid+'\')">✕ Del</button></td></tr>';
        });
        tbody.innerHTML=html;
    }).catch(function(err){tbody.innerHTML='<tr><td colspan="14" class="de-empty">Error: '+err.message+'</td></tr>';});
}

function openModal(type, editId) {
    currentAction = editId ? 'edit' : 'create';
    currentEditId = editId || null;
    currentPhotoUuid = editId || null;
    currentPhotoType = type;
    document.getElementById('modalTitle').textContent = editId ? 'Edit Record' : 'Add Record';
    document.getElementById('modalBody').innerHTML = '';
    if (type === 'se') {
        var cat = document.getElementById('seDeCategory').value;
        document.getElementById('modalBody').innerHTML = buildSEForm(cat) + buildPhotoField();
    } else if (type === 'tb') {
        var cat = document.getElementById('tbDeCategory').value;
        document.getElementById('modalBody').innerHTML = buildTBForm(cat) + buildPhotoField();
    } else if (type === 'ec') {
        document.getElementById('modalBody').innerHTML = buildECForm() + buildPhotoField();
    }
    document.getElementById('dataModal').classList.add('show');
}

function buildPhotoField() {
    var isEdit = currentAction === 'edit';
    var hint = isEdit
        ? 'Leave empty to keep the existing photo.'
        : 'Photo is required. Select a photo to save this record.';
    var statusColor = isEdit ? '#059669' : '#f59e0b';
    var statusText = isEdit ? '📸 Existing photo will be kept if no new photo selected.' : '📌 Photo is required before saving a new record.';
    return '<div class="photo-field-section">' +
        '<label>Participant Photo' + (isEdit ? '' : ' *') + '</label>' +
        '<div class="photo-input-row">' +
            '<div class="photo-preview" id="photoPreview"><span>📷</span></div>' +
            '<div style="flex:1;min-width:180px;">' +
                '<input type="file" id="photoFileInput" accept="image/*" style="font-size:0.75rem;" onchange="previewSelectedPhoto(event)">' +
                '<div class="photo-hint">' + hint + '</div>' +
                '<div class="photo-status" id="photoUploadStatus" style="color:' + statusColor + ';">' + statusText + '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
}

function previewSelectedPhoto(event) {
    var file = event.target.files[0];
    var preview = document.getElementById('photoPreview');
    var status = document.getElementById('photoUploadStatus');
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        if (preview) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
        }
        if (status) {
            status.innerHTML = '✅ Photo selected: ' + file.name;
            status.style.color = '#059669';
        }
    };
    reader.readAsDataURL(file);
}

function closeModal(){
    document.getElementById('dataModal').classList.remove('show');
}
function setVal(id,val){var el=document.getElementById(id);if(el)el.value=val||'';}
function getVal(id){var el=document.getElementById(id);return el?el.value:'';}

function buildSEForm(cat) {
    var ro = regions.map(function(r){return '<option value="'+r+'">'+r+'</option>';}).join('');
    var fields = '';
    if (cat === 'attainment') {
        fields = '<div class="form-group"><label class="form-label">% Attainment</label><input class="form-control-sm" id="seAttain" type="number" step="0.01"></div>' +
            '<div class="form-group"><label class="form-label">Actual Volume</label><input class="form-control-sm" id="seActualVol" type="number" step="0.01"></div>' +
            '<div class="form-group"><label class="form-label">Budget</label><input class="form-control-sm" id="seBudget" type="number" step="0.01"></div>';
    } else {
        fields = '<div class="form-group"><label class="form-label">Revenue</label><input class="form-control-sm" id="seRevenue" type="number" step="0.01"></div>' +
            '<div class="form-group"><label class="form-label">Actual CM</label><input class="form-control-sm" id="seActCm" type="number" step="0.01"></div>' +
            '<div class="form-group"><label class="form-label">Price/LF</label><input class="form-control-sm" id="sePriceLf" type="number" step="0.01"></div>';
    }
    return '<div class="form-grid">' +
        '<div class="form-group"><label class="form-label">Region</label><select class="form-control-sm" id="seRegion">'+ro+'</select></div>' +
        '<div class="form-group"><label class="form-label">Name *</label><input class="form-control-sm" id="seName" placeholder="Full name"></div>' +
        '<div class="form-group"><label class="form-label">Area</label><input class="form-control-sm" id="seArea" placeholder="Area"></div>' +
        '<div class="form-group"><label class="form-label">Position</label><input class="form-control-sm" id="sePosition" placeholder="Position"></div>' +
        fields +
        '<div class="form-group" style="display:none;"><input class="form-control-sm" id="seGrowth" value="0"></div>' +
    '</div>';
}

function buildTBForm(cat) {
    var ro = regions.map(function(r){return '<option value="'+r+'">'+r+'</option>';}).join('');
    var fields = '';
    if (cat === 'growth' || cat === 'margin') {
        fields = '<div class="form-group"><label class="form-label">Growth %</label><input class="form-control-sm" id="tbGrowth" type="number" step="0.01"></div>' +
            '<div class="form-group"><label class="form-label">Last Month</label><input class="form-control-sm" id="tbLastMonth" type="number" step="0.01"></div>' +
            '<div class="form-group"><label class="form-label">Current Month</label><input class="form-control-sm" id="tbCurrentMonth" type="number" step="0.01"></div>';
    } else {
        fields = '<div class="form-group"><label class="form-label">% Attainment</label><input class="form-control-sm" id="tbAttain" type="number" step="0.01"></div>' +
            '<div class="form-group"><label class="form-label">Actual</label><input class="form-control-sm" id="tbActual" type="number" step="0.01"></div>' +
            '<div class="form-group"><label class="form-label">Budget</label><input class="form-control-sm" id="tbBudget" type="number" step="0.01"></div>';
    }
    return '<div class="form-grid">' +
        '<div class="form-group"><label class="form-label">Region</label><select class="form-control-sm" id="tbRegion">'+ro+'</select></div>' +
        '<div class="form-group"><label class="form-label">Sales Office</label><input class="form-control-sm" id="tbSalesOffice" placeholder="Office"></div>' +
        '<div class="form-group"><label class="form-label">Name *</label><input class="form-control-sm" id="tbName" placeholder="Full name"></div>' +
        '<div class="form-group"><label class="form-label">Area</label><input class="form-control-sm" id="tbArea" placeholder="Area"></div>' +
        '<div class="form-group"><label class="form-label">Position</label><input class="form-control-sm" id="tbPosition" placeholder="Position"></div>' +
        fields +
        '<div class="form-group" style="display:none;"><input class="form-control-sm" id="tbRevenue" value="0"></div>' +
    '</div>';
}

function buildECForm() {
    var ro = regions.map(function(r){return '<option value="'+r+'">'+r+'</option>';}).join('');
    return '<div class="form-grid">' +
        '<div class="form-group"><label class="form-label">Quarter/Year</label><input class="form-control-sm" id="ecQuarterYear" placeholder="e.g. Q2-2026"></div>' +
        '<div class="form-group"><label class="form-label">Region</label><select class="form-control-sm" id="ecRegion">'+ro+'</select></div>' +
        '<div class="form-group"><label class="form-label">Top Vol Name</label><input class="form-control-sm" id="ecTopVolName" placeholder="Name"></div>' +
        '<div class="form-group"><label class="form-label">Top Vol Area</label><input class="form-control-sm" id="ecTopVolArea" placeholder="Area"></div>' +
        '<div class="form-group"><label class="form-label">Top Vol Position</label><input class="form-control-sm" id="ecTopVolPos" placeholder="Position"></div>' +
        '<div class="form-group"><label class="form-label">Top Vol Value</label><input class="form-control-sm" id="ecTopVolValue" type="number" step="0.01"></div>' +
        '<div class="form-group"><label class="form-label">Top CM Name</label><input class="form-control-sm" id="ecTopCmName" placeholder="Name"></div>' +
        '<div class="form-group"><label class="form-label">Top CM Area</label><input class="form-control-sm" id="ecTopCmArea" placeholder="Area"></div>' +
        '<div class="form-group"><label class="form-label">Top CM Position</label><input class="form-control-sm" id="ecTopCmPos" placeholder="Position"></div>' +
        '<div class="form-group"><label class="form-label">Top CM Value</label><input class="form-control-sm" id="ecTopCmValue" type="number" step="0.01"></div>' +
        '<div class="form-group"><label class="form-label">Total Volume</label><input class="form-control-sm" id="ecTotalVol" type="number" step="0.01"></div>' +
        '<div class="form-group"><label class="form-label">Total CM</label><input class="form-control-sm" id="ecTotalCm" type="number" step="0.01"></div>' +
    '</div>';
}

function editRecord(type, uuid) {
    openModal(type, uuid);
    var endpoint = type==='se'?'/data/se-list':(type==='tb'?'/data/tb-list':'/data/ec-list');
    fetch(endpoint+'?_='+Date.now()).then(function(r){return r.json();}).then(function(res){
        if(!res.success||!res.data)return;
        var record=null;res.data.forEach(function(r){if(r.uuid===uuid)record=r;});if(!record)return;
        if (record.photo) {
            var preview = document.getElementById('photoPreview');
            var status = document.getElementById('photoUploadStatus');
            if (preview) {
                preview.innerHTML = '<img src="/uploads/photos/' + record.photo + '" alt="Photo" onerror="this.parentElement.innerHTML=\'<span>📷</span>\'">';
            }
            if (status) {
                status.innerHTML = '📸 Existing photo detected. Select a new one to replace it.';
                status.style.color = '#059669';
            }
        } else {
            var status = document.getElementById('photoUploadStatus');
            if (status) {
                status.innerHTML = '⚠️ No photo found for this record. Please upload one.';
                status.style.color = '#f59e0b';
            }
        }
        if(type==='se'){
            setVal('seRegion',record.region);setVal('seName',record.name);setVal('seArea',record.area||'');setVal('sePosition',record.position||'');
            var el=document.getElementById('seAttain');if(el)el.value=record.attainment_percent||'';
            el=document.getElementById('seActualVol');if(el)el.value=record.actual_volume||'';
            el=document.getElementById('seBudget');if(el)el.value=record.budget||'';
            el=document.getElementById('seRevenue');if(el)el.value=record.revenue||'';
            el=document.getElementById('seActCm');if(el)el.value=record.actual_cm||'';
            el=document.getElementById('sePriceLf');if(el)el.value=record.price_lf||'';
            el=document.getElementById('seMargin');if(el)el.value=record.margin||'';
        } else if(type==='tb'){
            setVal('tbRegion',record.region);setVal('tbSalesOffice',record.sales_office||'');setVal('tbName',record.name);
            setVal('tbArea',record.area||'');setVal('tbPosition',record.position||'');
            var el=document.getElementById('tbGrowth');if(el)el.value=record.growth_percent||'';
            el=document.getElementById('tbLastMonth');if(el)el.value=record.last_month||'';
            el=document.getElementById('tbCurrentMonth');if(el)el.value=record.current_month||'';
            el=document.getElementById('tbAttain');if(el)el.value=record.attainment_percent||'';
            el=document.getElementById('tbActual');if(el)el.value=record.actual||'';
            el=document.getElementById('tbBudget');if(el)el.value=record.budget||'';
        } else if(type==='ec'){
            setVal('ecQuarterYear',record.quarter_year);setVal('ecRegion',record.region);
            setVal('ecTopVolName',record.top_volume_name||'');setVal('ecTopVolArea',record.top_volume_area||'');
            setVal('ecTopVolPos',record.top_volume_position||'');setVal('ecTopVolValue',record.top_volume_value);
            setVal('ecTopCmName',record.top_cm_name||'');setVal('ecTopCmArea',record.top_cm_area||'');
            setVal('ecTopCmPos',record.top_cm_position||'');setVal('ecTopCmValue',record.top_cm_value);
            setVal('ecTotalVol',record.total_volume);setVal('ecTotalCm',record.total_cm);
        }
    }).catch(function(err){console.error(err);});
}

function saveRecord() {
    var type = currentTab;
    var isEdit = currentAction==='edit';
    var id = currentEditId;

    // Build FormData (supports file upload)
    var formData = new FormData();

    // Validate photo for new records
    var photoInput = document.getElementById('photoFileInput');
    var photoFile = photoInput && photoInput.files.length > 0 ? photoInput.files[0] : null;
    if (!isEdit && !photoFile) {
        alert('Photo is required. Please select a participant photo before saving.');
        return;
    }
    if (photoFile) {
        formData.append('photo', photoFile);
    }

    if(type==='se'){
        var cat = document.getElementById('seDeCategory').value;
        formData.append('region', getVal('seRegion'));
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
        if(cat==='attainment'){
            formData.append('attainment_percent', parseFloat(document.getElementById('seAttain')?.value)||0);
            formData.append('actual_volume', parseFloat(document.getElementById('seActualVol')?.value)||0);
            formData.append('budget', parseFloat(document.getElementById('seBudget')?.value)||0);
        } else {
            formData.append('revenue', parseFloat(document.getElementById('seRevenue')?.value)||0);
            formData.append('actual_cm', parseFloat(document.getElementById('seActCm')?.value)||0);
            formData.append('price_lf', parseFloat(document.getElementById('sePriceLf')?.value)||0);
            formData.append('margin', parseFloat(document.getElementById('seMargin')?.value)||0);
        }
        var url = isEdit ? '/data/se-update/'+id : '/data/se-create';
        var method = 'POST';
    } else if(type==='tb'){
        var cat = document.getElementById('tbDeCategory').value;
        formData.append('region', getVal('tbRegion'));
        formData.append('sales_office', getVal('tbSalesOffice'));
        formData.append('name', getVal('tbName'));
        formData.append('area', getVal('tbArea'));
        formData.append('position', getVal('tbPosition'));
        formData.append('category', cat);
        formData.append('growth_percent', 0);
        formData.append('attainment_percent', 0);
        formData.append('actual', 0);
        formData.append('budget', 0);
        formData.append('last_month', 0);
        formData.append('current_month', 0);
        formData.append('revenue', parseFloat(getVal('tbRevenue'))||0);
        if(cat==='growth' || cat==='margin'){
            formData.append('growth_percent', parseFloat(document.getElementById('tbGrowth')?.value)||0);
            formData.append('last_month', parseFloat(document.getElementById('tbLastMonth')?.value)||0);
            formData.append('current_month', parseFloat(document.getElementById('tbCurrentMonth')?.value)||0);
        } else {
            formData.append('attainment_percent', parseFloat(document.getElementById('tbAttain')?.value)||0);
            formData.append('actual', parseFloat(document.getElementById('tbActual')?.value)||0);
            formData.append('budget', parseFloat(document.getElementById('tbBudget')?.value)||0);
        }
        var url = isEdit ? '/data/tb-update/'+id : '/data/tb-create';
        var method = 'POST';
    } else if(type==='ec'){
        formData.append('quarter_year', getVal('ecQuarterYear'));
        formData.append('region', getVal('ecRegion'));
        formData.append('top_volume_name', getVal('ecTopVolName'));
        formData.append('top_volume_area', getVal('ecTopVolArea'));
        formData.append('top_volume_position', getVal('ecTopVolPos'));
        formData.append('top_volume_value', parseFloat(getVal('ecTopVolValue'))||0);
        formData.append('top_cm_name', getVal('ecTopCmName'));
        formData.append('top_cm_area', getVal('ecTopCmArea'));
        formData.append('top_cm_position', getVal('ecTopCmPos'));
        formData.append('top_cm_value', parseFloat(getVal('ecTopCmValue'))||0);
        formData.append('total_volume', parseFloat(getVal('ecTotalVol'))||0);
        formData.append('total_cm', parseFloat(getVal('ecTotalCm'))||0);
        var url = isEdit ? '/data/ec-update/'+id : '/data/ec-create';
        var method = 'POST';
    }

    if(!formData.get('name')&&type!=='ec'){alert('Name is required!');return;}

    var btn=document.getElementById('modalSaveBtn');
    btn.disabled=true;btn.textContent='Saving...';
    fetch(url,{method:method,body:formData})
        .then(function(r){return r.json();})
        .then(function(res){
            if(res.success){
                var status = document.getElementById('photoUploadStatus');
                if (status) {
                    status.innerHTML = '✅ Photo saved successfully!';
                    status.style.color = '#059669';
                }
                alert(res.message||'Saved with photo!');
                closeModal();
                refreshData(type);
            }
            else{alert('Error: '+(res.error||'Save failed'));}
        })
        .catch(function(err){alert('Error: '+err.message);})
        .finally(function(){btn.disabled=false;btn.textContent='Save Record';});
}

function deleteRecord(type,uuid){
    if(!confirm('Delete this record?'))return;
    var url=type==='se'?'/data/se-delete/'+uuid:(type==='tb'?'/data/tb-delete/'+uuid:'/data/ec-delete/'+uuid);
    fetch(url,{method:'DELETE'}).then(function(r){return r.json();}).then(function(res){
        if(res.success){refreshData(type);alert('Deleted!');}
        else{alert('Error: '+(res.error||'Delete failed'));}
    }).catch(function(err){alert('Error: '+err.message);});
}

function clearDataType(type) {
    var labels = {se:'Sales Excellence', tb:'Top Branch Recognition', ec:'Elite Circle'};
    if(!confirm('Are you sure you want to delete ALL '+labels[type]+' data? This will also clear related leaderboard and summary data.'))return;
    fetch('/data/clear-type/'+type,{method:'POST'}).then(function(r){return r.json();}).then(function(res){
        if(res.success){alert(res.message);refreshData(currentTab);}
        else{alert('Error: '+(res.error||'Clear failed'));}
    }).catch(function(err){alert('Error: '+err.message);});
}

function clearAllData() {
    if(!confirm('⚠️ WARNING: This will permanently delete ALL data in the system including:\n\n- Sales Excellence Data\n- Top Branch Recognition Data\n- Elite Circle Data\n- Leaderboard Data\n- Analytics Data\n- Awardee Photos\n\nThis action CANNOT be undone. Are you sure?'))return;
    if(!confirm('FINAL CONFIRMATION: Delete ALL system data?'))return;
    fetch('/data/clear-all',{method:'POST'}).then(function(r){return r.json();}).then(function(res){
        if(res.success){alert(res.message);refreshData(currentTab);}
        else{alert('Error: '+(res.error||'Clear failed'));}
    }).catch(function(err){alert('Error: '+err.message);});
}

document.addEventListener('DOMContentLoaded',function(){loadSEData();});
</script>
<?= $this->endSection() ?>