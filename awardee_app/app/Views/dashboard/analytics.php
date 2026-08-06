<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- ===== EXECUTIVE ANALYTICS DASHBOARD ===== -->
<link rel="stylesheet" href="/css/executive/theme.css">
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.2"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<style>
/* ===== PAGE BACKGROUND: PHINMA Plaza (like sidebar style) ===== */
.ex-root {
    position: relative;
}
.ex-root::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('/PHINMA-Plaza.jpg') center / cover no-repeat fixed;
    z-index: -2;
    pointer-events: none;
}
.ex-root::after {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.75);
    z-index: -1;
    pointer-events: none;
}

/* ===== OVERRIDE: Remove panel styling from top header ===== */
.ex-top-header {
    background: transparent !important;
    border: none !important;
    border-left: none !important;
    border-radius: 0 !important;
    padding: 0 !important;
    box-shadow: none !important;
}
.ex-top-header:hover { box-shadow: none !important; }

/* ===== LAYOUT: Photo Square + Search + Filters (Right Panel) ===== */
.ex-right-panel {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    width: 320px;
    flex-shrink: 0;
}

/* ===== CORPORATE PARTICIPANT PHOTO CARD ===== */
.ex-photo-square {
    width: 100%;
    border-radius: var(--radius-xl);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: #fff;
    padding: 1.75rem 1.25rem 1.25rem;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
    min-height: 470px;
    background: url('/Image section bg.png') center / cover no-repeat;
    transition: all var(--transition-slow);
}

.ex-photo-square::before {
    content: '';
    position: absolute;
    top: -30%;
    right: -20%;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

/* Corporate watermark */
.ex-photo-square .corp-watermark {
    position: absolute;
    bottom: 10px;
    right: 15px;
    font-size: 0.45rem;
    font-weight: 700;
    color: rgba(255,255,255,0.2);
    letter-spacing: 1px;
    text-transform: uppercase;
    pointer-events: none;
    z-index: 1;
}

/* Override theme.css rank backgrounds - use rank template images */
.ex-photo-square.rank-1st {
    background: url('/1st.png') center / cover no-repeat !important;
}
.ex-photo-square.rank-2nd {
    background: url('/2nd.png') center / cover no-repeat !important;
}
.ex-photo-square.rank-3rd {
    background: url('/3rd.png') center / cover no-repeat !important;
}
.ex-photo-square.rank-grand-slam {
    background: url('/1st.png') center / cover no-repeat !important;
}

.ex-photo-square::after {
    content: '';
    position: absolute;
    bottom: -20%;
    left: -20%;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(255,255,255,0.04) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

.ex-photo-square .photo-placeholder {
    width: 100%;
    max-width: 270px;
    aspect-ratio: 1 / 1;
    border-radius: 12px;
    background: transparent;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.8rem;
    color: rgba(255,255,255,0.4);
    margin: 0;
    transition: all var(--transition-slow);
    overflow: hidden;
    flex-shrink: 0;
    position: absolute;
    top: 5rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1;
}

.ex-photo-square .photo-placeholder img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 12px;
    display: block;
    max-width: 100%;
    max-height: 100%;
}

.ex-photo-square .photo-placeholder.has-photo {
    border: none;
    background: transparent;
    box-shadow: none;
}

.ex-photo-square .photo-label {
    font-size: 0.55rem;
    font-weight: 700;
    color: rgba(255,255,255,0.4);
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-top: 0.15rem;
    position: relative;
    z-index: 1;
    display: block;
}

.ex-photo-square .photo-name {
    font-size: 2.8rem;
    font-weight: 900;
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1.2 !important;
    position: absolute !important;
    top: 19.5rem !important;
    right: auto !important;
    bottom: auto !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    z-index: 1;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    text-transform: uppercase;
    color: #0B7A3B;
    width: 270px !important;
    max-width: 270px !important;
    min-width: 0 !important;
    box-sizing: border-box !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    display: block !important;
}

.ex-photo-square .photo-position {
    font-size: 0.66rem;
    color: #6B7280;
    font-weight: 700;
    margin: 0;
    position: absolute;
    top: 23rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    width: 100%;
}

.ex-photo-square .photo-category {
    font-size: 0.68rem;
    color: #FFFFFF;
    font-weight: 700;
    margin: 0;
    position: absolute;
    top: 25.5rem;
    left: 55%;
    transform: translateX(-50%);
    z-index: 1;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    width: 100%;
}

/* Search bar inside right panel */
.ex-right-search {
    display: flex;
    gap: 0.5rem;
    width: 100%;
}

.ex-right-search input {
    flex: 1;
    padding: 0.65rem 0.85rem 0.65rem 2.4rem;
    border: 1.5px solid var(--border-color);
    border-radius: var(--radius-sm);
    font-size: 0.78rem;
    font-weight: 500;
    color: var(--text-primary);
    background: #FAFBFC url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%239494a8' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat 0.75rem center;
    outline: none;
    transition: all var(--transition-fast);
}

.ex-right-search input:focus {
    border-color: var(--ugc-green);
    box-shadow: 0 0 0 3px rgba(11, 122, 59, 0.1);
}

.ex-right-search button {
    padding: 0.65rem 1.2rem;
    border: none;
    border-radius: var(--radius-sm);
    background: var(--ugc-green-gradient);
    color: #fff;
    font-size: 0.78rem;
    font-weight: 700;
    cursor: pointer;
    white-space: nowrap;
    transition: all var(--transition-fast);
}

.ex-right-search button:hover {
    background: var(--ugc-green-dark);
    box-shadow: 0 4px 14px rgba(11, 122, 59, 0.3);
}

.ex-right-search button.reset-btn {
    background: #6B7280;
}

.ex-right-search button.reset-btn:hover {
    background: #4B5563;
}

/* ===== EMPLOYEE DETAIL TABLE (below search) ===== */
.ex-detail-table-wrap {
    width: 100%;
    background: var(--card-bg);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

/* ===== PARTICIPANT MONTHLY TREND (mini chart) ===== */
.ex-participant-trend {
    width: 100%;
    background: var(--card-bg);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    display: none;
}

.ex-participant-trend.show { display: block; }

.ex-participant-trend .ept-header {
    padding: 0.45rem 0.75rem;
    background: #1e293b;
    color: #fff;
    font-size: 0.62rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ex-participant-trend .ept-body {
    padding: 0.5rem;
}

.ex-participant-trend .ept-chart {
    width: 100%;
    height: 80px;
}

.ex-detail-table-header {
    padding: 0.55rem 0.85rem;
    background: #374151;
    color: #fff;
    font-size: 0.68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ex-detail-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.7rem;
}

.ex-detail-table th {
    padding: 0.45rem 0.5rem;
    text-align: center;
    border: 1px solid #d1d5db;
    font-size: 0.58rem;
    text-transform: uppercase;
    color: #475569;
    font-weight: 800;
    letter-spacing: 0.3px;
    background: #f8fafc;
    white-space: nowrap;
}

.ex-detail-table td {
    padding: 0.5rem 0.4rem;
    border: 1px solid #e2e8f0;
    text-align: center;
    font-size: 0.65rem;
    color: #334155;
    vertical-align: middle;
}

.ex-detail-table td.status-active { color: var(--ugc-green); font-weight: 700; }
.ex-detail-table td.status-pending { color: var(--ugc-orange); font-weight: 700; }

/* ===== MAIN CONTENT AREA (Left side) ===== */
.ex-main-content {
    flex: 1;
    min-width: 0;
}

.ex-dashboard-layout {
    display: flex;
    gap: 1.25rem;
    align-items: flex-start;
}

@media (max-width: 1200px) {
    .ex-dashboard-layout { flex-direction: column; }
    .ex-right-panel { width: 100%; }
    .ex-photo-square { min-height: 200px; aspect-ratio: auto; flex-direction: row; flex-wrap: wrap; gap: 0.75rem; }
    .ex-photo-square .photo-placeholder { width: 200px; max-width: 200px; height: 200px; aspect-ratio: auto; }
}

/* ===== SMART ANALYTICS AI SECTION ===== */
.ex-ai-section {
    display: none;
    margin-top: 1rem;
    background: var(--card-bg);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    animation: slideDown 0.4s cubic-bezier(0.22, 1, 0.36, 1);
}
.ex-ai-section.show { display: block; }

.ex-ai-header {
    padding: 0.65rem 1rem;
    background: linear-gradient(135deg, #1A1A2E, #2D2D44);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ex-ai-header .ai-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.6rem;
    background: rgba(255,255,255,0.12);
    border-radius: 20px;
    font-size: 0.6rem;
    font-weight: 700;
    color: #FFD700;
    border: 1px solid rgba(255,215,0,0.3);
}

.ex-ai-body {
    padding: 1rem 1.25rem;
}

.ex-ai-metrics {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .ex-ai-metrics { grid-template-columns: repeat(2, 1fr); }
}

.ex-ai-metric {
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: var(--radius-sm);
    border: 1px solid #e2e8f0;
    text-align: center;
}

.ex-ai-metric .ai-m-label {
    font-size: 0.58rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 0.15rem;
}

.ex-ai-metric .ai-m-value {
    font-size: 1rem;
    font-weight: 800;
    color: #1e293b;
}

.ex-ai-metric .ai-m-value.green { color: var(--ugc-green); }
.ex-ai-metric .ai-m-value.blue { color: var(--ugc-blue); }
.ex-ai-metric .ai-m-value.red { color: var(--ugc-red); }
.ex-ai-metric .ai-m-value.gold { color: #B8860B; }

.ex-ai-findings {
    padding: 0.75rem 1rem;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: var(--radius-sm);
    margin-bottom: 0.75rem;
}

.ex-ai-findings .findings-title {
    font-size: 0.65rem;
    font-weight: 800;
    color: #166534;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 0.4rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.ex-ai-findings .findings-text {
    font-size: 0.78rem;
    color: #374151;
    line-height: 1.6;
}

.ex-ai-recommendations {
    padding: 0.75rem 1rem;
    background: #f0f7ff;
    border: 1px solid #bfdbfe;
    border-radius: var(--radius-sm);
}

.ex-ai-recommendations .rec-title {
    font-size: 0.65rem;
    font-weight: 800;
    color: #1e40af;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 0.4rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.ex-ai-recommendations .rec-text {
    font-size: 0.78rem;
    color: #374151;
    line-height: 1.6;
}

.ex-ai-recommendations .rec-item {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 0.3rem 0;
    font-size: 0.75rem;
    color: #334155;
    line-height: 1.4;
}

.ex-ai-recommendations .rec-item .rec-bullet {
    color: var(--ugc-blue);
    font-weight: 700;
    flex-shrink: 0;
}

/* ===== EXTRA SMALL KPI CARDS ===== */
.ex-kpi-xs {
    padding: 0.55rem 0.75rem !important;
}
.ex-kpi-xs .kpi-value {
    font-size: 1rem !important;
}
.ex-kpi-xs .kpi-icon {
    width: 26px !important;
    height: 26px !important;
    font-size: 0.75rem !important;
}
.ex-kpi-xs .kpi-label {
    font-size: 0.5rem !important;
}
.ex-kpi-xs .kpi-sub {
    font-size: 0.55rem !important;
}
.ex-kpi-xs .kpi-top {
    margin-bottom: 0.15rem !important;
}

/* Empty state */
.ex-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 1.5rem;
    text-align: center;
}
.ex-empty-state .empty-icon { font-size: 3.5rem; margin-bottom: 0.75rem; opacity: 0.5; }
.ex-empty-state h3 { font-size: 1rem; font-weight: 700; color: var(--text-primary); margin: 0 0 0.3rem; }
.ex-empty-state p { font-size: 0.8rem; color: var(--text-muted); max-width: 350px; margin: 0; }

/* ===== DARK MODE: Header title text ===== */
body.ex-dark-mode .ex-header-title h1 {
    color: #000000 !important;
}
body.ex-dark-mode .ex-header-title h1 span[style*="color: var(--ugc-red)"] {
    color: #E31C23 !important;
}

/* Loading spinner */
.ex-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    gap: 0.5rem;
}

.ex-loading .spinner {
    width: 20px;
    height: 20px;
    border: 3px solid var(--border-color);
    border-top-color: var(--ugc-green);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-12px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}

/* ===== PHOTO EXPAND - ENLARGE IN PLACE ===== */
/* Overlay backdrop */
.ex-photo-expand-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 9998;
    display: none;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.ex-photo-expand-overlay.show {
    display: flex;
    opacity: 1;
}

/* The photo square when expanded - stays in DOM, just scaled up */
.ex-photo-square.expanded {
    position: fixed !important;
    top: 50% !important;
    left: 50% !important;
    transform: translate(-50%, -50%) scale(1.35) !important;
    z-index: 9999 !important;
    width: 320px !important;
    max-width: 320px !important;
    min-height: 470px !important;
    cursor: default !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5) !important;
    border-radius: 16px !important;
    margin: 0 !important;
    transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1) !important;
}

/* Download button shown when expanded */
.ex-photo-download-btn {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10000;
    padding: 0.7rem 1.8rem;
    border: none;
    border-radius: 25px;
    background: linear-gradient(135deg, #0B7A3B, #059669);
    color: #fff;
    font-size: 0.8rem;
    font-weight: 800;
    cursor: pointer;
    display: none;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 8px 25px rgba(11, 122, 59, 0.4);
    transition: all 0.2s;
}

.ex-photo-download-btn:hover {
    background: linear-gradient(135deg, #0d6e2f, #047857);
    transform: translateX(-50%) scale(1.05);
}

.ex-photo-download-btn.show {
    display: flex;
}

/* Close button for expanded view */
.ex-photo-close-btn {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    transition: all 0.15s;
}

.ex-photo-close-btn:hover {
    background: rgba(255, 255, 255, 0.4);
    transform: rotate(90deg);
}

.ex-photo-close-btn.show {
    display: flex;
}

/* Make photo square clickable to expand */
.ex-photo-square.clickable {
    cursor: pointer;
}

.ex-photo-square.clickable:hover {
    transform: scale(1.01);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

/* ===== EXPORT FORMAT SELECTION MODAL (JPEG/PNG) ===== */
.ex-export-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.55);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 10001;
    display: none;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.25s ease;
}

.ex-export-modal-overlay.show {
    display: flex;
    opacity: 1;
}

.ex-export-modal {
    width: 90%;
    max-width: 380px;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    transform: translateY(20px) scale(0.95);
    transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}

.ex-export-modal-overlay.show .ex-export-modal {
    transform: translateY(0) scale(1);
}

.ex-export-modal-header {
    padding: 0.85rem 1.25rem;
    background: linear-gradient(135deg, #0B7A3B, #059669);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}

.ex-export-modal-header .export-title {
    font-size: 0.8rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.ex-export-modal-header .export-close-btn {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s;
}

.ex-export-modal-header .export-close-btn:hover {
    background: rgba(255, 255, 255, 0.4);
    transform: rotate(90deg);
}

.ex-export-modal-body {
    padding: 1.25rem;
}

.ex-export-format-options {
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
}

.ex-export-format-option {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.85rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.15s;
    background: #f8fafc;
}

.ex-export-format-option:hover {
    border-color: var(--ugc-green);
    background: #f0fdf4;
}

.ex-export-format-option.selected {
    border-color: var(--ugc-green);
    background: #f0fdf4;
    box-shadow: 0 0 0 3px rgba(11, 122, 59, 0.1);
}

.ex-export-format-option .format-icon {
    width: 42px;
    height: 42px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    font-weight: 800;
    color: #fff;
    flex-shrink: 0;
}

.ex-export-format-option .format-icon.png {
    background: linear-gradient(135deg, #2D6CDF, #1e40af);
}

.ex-export-format-option .format-icon.jpg {
    background: linear-gradient(135deg, #F59E0B, #D97706);
}

.ex-export-format-option .format-info {
    flex: 1;
}

.ex-export-format-option .format-name {
    font-size: 0.8rem;
    font-weight: 800;
    color: #1e293b;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.ex-export-format-option .format-desc {
    font-size: 0.65rem;
    color: #64748b;
    margin-top: 2px;
}

.ex-export-format-option .format-check {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: transparent;
    font-size: 0.65rem;
    font-weight: 800;
    transition: all 0.15s;
}

.ex-export-format-option.selected .format-check {
    background: var(--ugc-green);
    border-color: var(--ugc-green);
    color: #fff;
}

.ex-export-modal-footer {
    padding: 0.75rem 1.25rem 1.25rem;
    display: flex;
    gap: 0.5rem;
}

.ex-export-btn {
    flex: 1;
    padding: 0.7rem 1rem;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #0B7A3B, #059669);
    color: #fff;
    font-size: 0.78rem;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.15s;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.ex-export-btn:hover {
    background: linear-gradient(135deg, #0d6e2f, #047857);
    box-shadow: 0 4px 14px rgba(11, 122, 59, 0.3);
}

.ex-export-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.ex-export-cancel-btn {
    padding: 0.7rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    background: #fff;
    color: #64748b;
    font-size: 0.75rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s;
}

.ex-export-cancel-btn:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

/* ===== AI CONVERSATION POPUP MODAL ===== */
.ex-ai-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.55);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.ex-ai-modal-overlay.show {
    display: flex;
    opacity: 1;
}

.ex-ai-modal {
    width: 90%;
    max-width: 720px;
    max-height: 85vh;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transform: translateY(20px) scale(0.95);
    transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}

.ex-ai-modal-overlay.show .ex-ai-modal {
    transform: translateY(0) scale(1);
}

.ex-ai-modal-header {
    padding: 0.85rem 1.25rem;
    background: linear-gradient(135deg, #1A1A2E, #2D2D44);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}

.ex-ai-modal-header .ai-title {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.8rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.ex-ai-modal-header .ai-title img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 215, 0, 0.5);
}

.ex-ai-modal-header .ai-close-btn {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s;
}

.ex-ai-modal-header .ai-close-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.ex-ai-modal-body {
    flex: 1;
    overflow-y: auto;
    padding: 1.25rem;
    background: #f8fafc;
}

/* Conversation bubbles */
.ex-ai-conversation {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}

.ex-ai-msg {
    display: flex;
    gap: 0.6rem;
    align-items: flex-start;
    max-width: 85%;
    animation: slideDown 0.4s cubic-bezier(0.22, 1, 0.36, 1);
}

.ex-ai-msg.ai {
    align-self: flex-start;
}

.ex-ai-msg.user {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.ex-ai-msg .msg-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 800;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

.ex-ai-msg.ai .msg-avatar {
    background: linear-gradient(135deg, #0B7A3B, #059669);
}

.ex-ai-msg.user .msg-avatar {
    background: linear-gradient(135deg, #2D6CDF, #1e40af);
}

.ex-ai-msg .msg-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.ex-ai-msg .msg-bubble {
    padding: 0.65rem 0.9rem;
    border-radius: 12px;
    font-size: 0.75rem;
    line-height: 1.6;
    color: #334155;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.ex-ai-msg.ai .msg-bubble {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-top-left-radius: 4px;
}

.ex-ai-msg.user .msg-bubble {
    background: linear-gradient(135deg, #0B7A3B, #059669);
    color: #fff;
    border-top-right-radius: 4px;
}

.ex-ai-msg .msg-time {
    font-size: 0.55rem;
    color: #94a3b8;
    margin-top: 0.2rem;
    display: block;
}

.ex-ai-msg.user .msg-time {
    text-align: right;
}

/* Typing indicator */
.ex-ai-typing {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.4rem 0;
}

.ex-ai-typing span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #94a3b8;
    animation: typingBounce 1.2s infinite;
}

.ex-ai-typing span:nth-child(2) { animation-delay: 0.2s; }
.ex-ai-typing span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typingBounce {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
    30% { transform: translateY(-4px); opacity: 1; }
}

/* Report content inside bubble */
.ex-ai-msg .msg-bubble .report-content {
    font-size: 0.72rem;
}

.ex-ai-msg .msg-bubble .report-content strong {
    color: #1e293b;
}

.ex-ai-msg .msg-bubble .report-content .report-section {
    margin-bottom: 0.4rem;
}

.ex-ai-msg .msg-bubble .report-content .report-section:last-child {
    margin-bottom: 0;
}

/* Modal footer */
.ex-ai-modal-footer {
    padding: 0.75rem 1.25rem;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    flex-shrink: 0;
}

.ex-ai-modal-footer input {
    flex: 1;
    padding: 0.6rem 0.9rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 20px;
    font-size: 0.75rem;
    outline: none;
    transition: border-color 0.15s;
}

.ex-ai-modal-footer input:focus {
    border-color: var(--ugc-green);
}

.ex-ai-modal-footer button {
    padding: 0.6rem 1.2rem;
    border: none;
    border-radius: 20px;
    background: var(--ugc-green-gradient);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s;
    white-space: nowrap;
}

.ex-ai-modal-footer button:hover {
    background: var(--ugc-green-dark);
    box-shadow: 0 4px 14px rgba(11, 122, 59, 0.3);
}
</style>

<div class="ex-root">

    <!-- ===== ROW 1: HEADER Title + Greeting Panel (full height) + Export Buttons ===== -->
    <div class="ex-top-header" style="display:flex;align-items:stretch;justify-content:space-between;gap:1rem;margin-bottom:0;">
        <div class="ex-header-title" style="flex:1;">
            <h1 style="margin:0;font-size:2.4rem;font-weight:900;color:var(--ugc-green);letter-spacing:-0.3px;line-height:1.3;">EXECUTIVE <span class="ex-title-accent">ANALYTICS</span> DASHBOARD</h1>
            <p style="margin:0.1rem 0 0;font-size:0.78rem;font-weight:500;color:var(--text-muted);">Performance Overview Across All Dashboards</p>
            <!-- KPI placed directly under the title -->
            <div class="ex-kpi-row ex-kpi-summary" id="exKpiRow" style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.55rem;margin-top:0.65rem;margin-bottom:0;">
                <div class="ex-kpi ex-kpi-xs" style="background:var(--card-bg);border-radius:var(--radius-lg);padding:1rem 1.25rem;border:1px solid var(--border-color);box-shadow:var(--shadow-sm);position:relative;overflow:hidden;">
                    <div style="position:absolute;top:0;left:0;width:100%;height:4px;background:var(--ugc-green-gradient);"></div>
                    <div class="kpi-top" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.3rem;">
                        <div class="kpi-label" style="font-size:0.6rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Monthly Revenue</div>
                    </div>
                    <div class="kpi-value" style="font-size:1.3rem;font-weight:800;color:var(--text-primary);line-height:1.1;margin-bottom:0.15rem;letter-spacing:-0.5px;" id="exMonthlyRevenue">₱0.00</div>
                    <div class="kpi-sub" style="font-size:0.6rem;font-weight:500;color:var(--text-muted);" id="exRevenueChange">vs previous month</div>
                </div>
                <div class="ex-kpi ex-kpi-xs" style="background:var(--card-bg);border-radius:var(--radius-lg);padding:1rem 1.25rem;border:1px solid var(--border-color);box-shadow:var(--shadow-sm);position:relative;overflow:hidden;">
                    <div style="position:absolute;top:0;left:0;width:100%;height:4px;background:var(--ugc-green-gradient);"></div>
                    <div class="kpi-top" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.3rem;">
                        <div class="kpi-label" style="font-size:0.6rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">AVS Growth</div>
                    </div>
                    <div class="kpi-value" style="font-size:1.3rem;font-weight:800;line-height:1.1;margin-bottom:0.15rem;letter-spacing:-0.5px;" id="exAvsGrowth">0.00%</div>
                    <div class="kpi-sub" style="font-size:0.6rem;font-weight:500;color:var(--text-muted);">Average growth rate</div>
                </div>
                <div class="ex-kpi ex-kpi-xs" style="background:var(--card-bg);border-radius:var(--radius-lg);padding:1rem 1.25rem;border:1px solid var(--border-color);box-shadow:var(--shadow-sm);position:relative;overflow:hidden;">
                    <div style="position:absolute;top:0;left:0;width:100%;height:4px;background:var(--ugc-green-gradient);"></div>
                    <div class="kpi-top" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.3rem;">
                        <div class="kpi-label" style="font-size:0.6rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Combined Volume</div>
                    </div>
                    <div class="kpi-value" style="font-size:1.3rem;font-weight:800;color:var(--text-primary);line-height:1.1;margin-bottom:0.15rem;letter-spacing:-0.5px;" id="exCombinedVolume">0</div>
                    <div class="kpi-sub" style="font-size:0.6rem;font-weight:500;color:var(--text-muted);">Total invoice quantity</div>
                </div>
                <div class="ex-kpi ex-kpi-xs" style="background:var(--card-bg);border-radius:var(--radius-lg);padding:1rem 1.25rem;border:1px solid var(--border-color);box-shadow:var(--shadow-sm);position:relative;overflow:hidden;">
                    <div style="position:absolute;top:0;left:0;width:100%;height:4px;background:var(--ugc-green-gradient);"></div>
                    <div class="kpi-top" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.3rem;">
                        <div class="kpi-label" style="font-size:0.6rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Combined CM</div>
                    </div>
                    <div class="kpi-value" style="font-size:1.3rem;font-weight:800;color:var(--text-primary);line-height:1.1;margin-bottom:0.15rem;letter-spacing:-0.5px;" id="exCombinedCm">₱0.00</div>
                    <div class="kpi-sub" style="font-size:0.6rem;font-weight:500;color:var(--text-muted);">Gross contribution margin</div>
                </div>
            </div>
        </div>
        <div class="ex-header-right" style="display:flex;flex-direction:column;gap:0.5rem;flex-shrink:0;align-items:stretch;">
            <div class="ex-greeting-panel" style="background:var(--card-bg);border:1px solid var(--border-color);border-radius:var(--radius-lg);padding:0.6rem 1rem;box-shadow:var(--shadow-sm);min-width:340px;display:flex;flex-direction:column;justify-content:center;flex:1;">
                <div style="display:flex;flex-direction:column;gap:0.75rem;">
                    <!-- Greeting text on top -->
                    <div style="flex:1;text-align:left;">
                        <span class="greeting-text" id="greetingText" style="font-size:0.85rem;font-weight:800;color:var(--ugc-green);display:block;line-height:1.3;">Good morning! Welcome.</span>
                        <span class="greeting-sub" id="greetingDate" style="font-size:0.62rem;font-weight:500;color:var(--text-muted);display:block;"></span>
                    </div>
                    <!-- Admin profile below greeting (clickable to settings) -->
                    <a href="<?= base_url('settings') ?>" class="ex-admin-profile" style="display:flex;align-items:center;gap:0.5rem;padding:0.35rem 0.75rem 0.35rem 0.45rem;background:var(--ugc-green-light);border-radius:var(--radius-md);border:1px solid rgba(11,122,59,0.15);justify-content:flex-start;text-decoration:none;cursor:pointer;transition:all var(--transition-fast);" onmouseover="this.style.background='#d4efe0';this.style.borderColor='rgba(11,122,59,0.3)';this.querySelector('.ex-admin-arrow').style.transform='translateX(3px)';" onmouseout="this.style.background='var(--ugc-green-light)';this.style.borderColor='rgba(11,122,59,0.15)';this.querySelector('.ex-admin-arrow').style.transform='translateX(0)';">
                        <div class="ex-admin-avatar" id="adminAvatar" style="width:36px;height:36px;border-radius:50%;background:var(--ugc-green-gradient);display:flex;align-items:center;justify-content:center;font-size:0.85rem;font-weight:700;color:#fff;flex-shrink:0;box-shadow:0 2px 8px rgba(11,122,59,0.25);overflow:hidden;">
                            <?php if (!empty($user['profile_pic'])): ?>
                                <img src="<?= base_url($user['profile_pic']) ?>" alt="Profile" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                            <?php else: ?>
                                <?= strtoupper(substr(esc($user['fullname'] ?? 'Admin'), 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <div class="ex-admin-info" style="text-align:left;flex:1;">
                            <span class="ex-admin-name" style="font-size:0.72rem;font-weight:800;color:var(--ugc-green);line-height:1.2;display:block;"><?= esc($user['fullname'] ?? 'Admin') ?></span>
                            <span class="ex-admin-role" style="font-size:0.58rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.3px;display:block;margin-top:1px;"><?= esc($user['role'] ?? 'Administrator') ?></span>
                        </div>
                        <svg class="ex-admin-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--ugc-green)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;transition:transform var(--transition-fast);"><path d="M9 18l6-6-6-6"/></svg>
                    </a>
                </div>
                <div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.5rem;padding-top:0.5rem;border-top:1px solid var(--border-color);">
                    <span style="font-size:0.6rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.3px;">Filter:</span>
                    <select class="ex-period-select" id="exMonth" style="padding:4px 24px 4px 8px;border:1px solid #d1d5db;border-radius:5px;font-size:10px;font-weight:600;color:#374151;background:#fff;cursor:pointer;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 12 12'%3E%3Cpath fill='%235a5a72' d='M6 8L1 3h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 6px center;">
                        <option value="1">January</option><option value="2">February</option><option value="3">March</option>
                        <option value="4">April</option><option value="5" selected>May</option><option value="6">June</option>
                        <option value="7">July</option><option value="8">August</option><option value="9">September</option>
                        <option value="10">October</option><option value="11">November</option><option value="12">December</option>
                    </select>
                    <select class="ex-period-select" id="exYear" style="padding:4px 24px 4px 8px;border:1px solid #d1d5db;border-radius:5px;font-size:10px;font-weight:600;color:#374151;background:#fff;cursor:pointer;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 12 12'%3E%3Cpath fill='%235a5a72' d='M6 8L1 3h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 6px center;"></select>
                    <button class="exec-top-btn" data-action="refresh" style="padding:4px 10px;border:none;border-radius:5px;background:#108a3b;color:#fff;font-size:10px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:3px;transition:all 0.15s;" onmouseover="this.style.background='#0d6e2f'" onmouseout="this.style.background='#108a3b'">🔄</button>
                    <button class="exec-top-btn" id="exDarkModeToggle" data-action="darkmode" style="padding:4px 10px;border:none;border-radius:5px;background:#374151;color:#fff;font-size:10px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:3px;transition:all 0.15s;" onmouseover="this.style.background='#1f2937'" onmouseout="this.style.background='#374151'" onclick="toggleExecutiveDarkMode()">🌙</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== CONTENT ROW: Performance Trend (left) + Right Panel (right) ===== -->
    <div class="ex-content-row" style="display:flex;gap:1.25rem;align-items:flex-start;margin-top:1rem;margin-bottom:0.75rem;">
        
        <!-- ===== LEFT: PERFORMANCE TREND + Regional + Top Performers ===== -->
        <div class="ex-trend-wrap" style="flex:1;min-width:0;">
            <div class="ex-card">
                <div class="ex-card-header"><h3>PERFORMANCE TREND</h3><span>Sales Excellence: Revenue | Top Branch: Growth % | Elite Circle: Volume & CM</span></div>
                <div class="ex-card-body" style="padding:0.5rem;">
                    <div id="exTrendChart" style="width:100%;height:280px;"></div>
                </div>
            </div>
            <!-- Regional + Top Performers under Performance Trend -->
            <div class="ex-charts-bottom" style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-top:0.75rem;">
                <div class="ex-card" style="margin:0;">
                    <div class="ex-card-header"><h3>REGIONAL PERFORMANCE</h3><span>Region · Revenue · Growth · CM · Volume</span></div>
                    <div class="ex-card-body" style="padding:0.5rem;">
                        <!-- Category selector like stock indicators -->
                        <div style="display:flex;align-items:center;gap:0.35rem;margin-bottom:0.6rem;flex-wrap:wrap;">
                            <span style="font-size:0.6rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Indicator:</span>
                            <button class="ex-region-cat-btn active" data-cat="revenue" onclick="exSetRegionalCategory('revenue')" style="padding:3px 10px;border:1.5px solid #0B7A3B;border-radius:14px;font-size:0.6rem;font-weight:800;background:#0B7A3B;color:#fff;cursor:pointer;transition:all 0.15s;">REVENUE</button>
                            <button class="ex-region-cat-btn" data-cat="growth" onclick="exSetRegionalCategory('growth')" style="padding:3px 10px;border:1.5px solid #E31C23;border-radius:14px;font-size:0.6rem;font-weight:800;background:#fff;color:#E31C23;cursor:pointer;transition:all 0.15s;">GROWTH%</button>
                            <button class="ex-region-cat-btn" data-cat="cm" onclick="exSetRegionalCategory('cm')" style="padding:3px 10px;border:1.5px solid #F59E0B;border-radius:14px;font-size:0.6rem;font-weight:800;background:#fff;color:#F59E0B;cursor:pointer;transition:all 0.15s;">CM</button>
                            <button class="ex-region-cat-btn" data-cat="volume" onclick="exSetRegionalCategory('volume')" style="padding:3px 10px;border:1.5px solid #2D6CDF;border-radius:14px;font-size:0.6rem;font-weight:800;background:#fff;color:#2D6CDF;cursor:pointer;transition:all 0.15s;">VOLUME</button>
                        </div>
                        <!-- AI Model + Stock Indicator layout -->
                        <div style="display:flex;gap:0.75rem;align-items:stretch;min-height:auto;">
                            <!-- AI Model Character - compact size to match region KPI panel -->
                            <div style="flex:1;min-width:0;display:flex;flex-direction:column;align-items:center;justify-content:center;overflow:hidden;padding:0.25rem 0;position:relative;">
                                <!-- AI Character Container - compact, matches region panel height -->
                                <div id="exAiCharacter" style="position:relative;width:100%;max-height:210px;cursor:pointer;user-select:none;transition:transform 0.2s ease;display:flex;align-items:center;justify-content:center;">
                                    <!-- AI Model Image - switches per category, compact fit -->
                                    <img id="exAiModelImg" src="/Ai1.png?v=20260805" alt="AI Analyst" style="width:auto;height:100%;max-height:200px;object-fit:contain;display:block;mix-blend-mode:multiply;background:transparent;">
                                </div>
                            </div>
                            <!-- Region Stock Indicators Side Panel (equal space) -->
                            <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:0.45rem;" id="exRegionStockList">
                                <!-- Region rows will be rendered by JS -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ex-card" style="margin:0;">
                    <div class="ex-card-header"><h3>TOP PERFORMERS</h3><a href="<?= base_url('leaderboard') ?>" title="Go to Leaderboard Dashboard" style="margin-left:auto;font-size:1.2rem;font-weight:800;color:var(--ugc-green);text-decoration:none;cursor:pointer;display:inline-flex;align-items:center;transition:all 0.15s;" onmouseover="this.style.color='#E31C23';this.style.transform='translateX(3px)'" onmouseout="this.style.color='var(--ugc-green)';this.style.transform='translateX(0)'">›</a></div>
                    <div class="ex-card-body" style="padding:0;overflow-y:auto;max-height:260px;">
                        <div class="ex-top10-table-wrap">
                            <table class="ex-top10-table" id="exTopPerformersTable">
                                <thead><tr><th style="width:30px;">#</th><th>Name</th><th>Dashboard</th><th style="text-align:right;">Points</th></tr></thead>
                                <tbody id="exTopPerformersBody">
                                    <tr><td colspan="4" style="text-align:center;padding:1.5rem;color:#94a3b8;">Search for a participant to view performance data.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== RIGHT PANEL: Photo Square + Search + Detail Table ===== -->
        <div class="ex-right-panel" style="width:320px;flex-shrink:0;">
            <!-- PHOTO SQUARE with Corporate Design -->
            <div class="ex-photo-square" id="exPhotoSquare">
                <div class="photo-placeholder" id="exPhotoPlaceholder">
                    <span id="exPhotoIcon">📷</span>
                </div>
                <div class="photo-name" id="exPhotoName">—</div>
                <div class="photo-position" id="exPhotoPosition">—</div>
                <div class="photo-category" id="exPhotoCategory">—</div>
            </div>

            <!-- PHOTO EXPAND OVERLAY + BUTTONS -->
            <div class="ex-photo-expand-overlay" id="exPhotoExpandOverlay" onclick="exClosePhotoModal()"></div>
            <button class="ex-photo-close-btn" id="exPhotoCloseBtn" onclick="exClosePhotoModal()">✕</button>
            <button class="ex-photo-download-btn" id="exPhotoDownloadBtn" onclick="exDownloadPhoto()">⬇️ Download Placement</button>

            <!-- DASHBOARD → CATEGORY → NAME SEARCH (two-step logic) -->
            <div class="ex-search-panel" style="display:flex;flex-direction:column;gap:0.4rem;width:100%;background:var(--card-bg);border:1px solid var(--border-color);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);padding:0.85rem 0.9rem;">
                <div style="display:flex;align-items:center;gap:0.4rem;margin-bottom:0.15rem;">
                    <span style="font-size:0.62rem;font-weight:800;color:var(--ugc-green);text-transform:uppercase;letter-spacing:0.5px;">🔍 SEARCH PARTICIPANT</span>
                </div>
                <label style="font-size:0.58rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">1. Select Dashboard</label>
                <select id="exDashboardFilter" onchange="exUpdateCategoryOptions()" style="width:100%;padding:0.5rem 0.6rem;border:1.5px solid var(--border-color);border-radius:var(--radius-sm);font-size:0.68rem;font-weight:600;color:var(--text-primary);background:#FAFBFC;outline:none;cursor:pointer;">
                    <option value="">-- Select Dashboard --</option>
                    <option value="se">Sales Excellence Awardee</option>
                    <option value="tb">Top Branch Recognition</option>
                    <option value="ec">Elite Circle</option>
                </select>

                <label style="font-size:0.58rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-top:0.2rem;">2. Select Category</label>
                <select id="exCategoryFilter" onchange="exLoadParticipantNames()" style="width:100%;padding:0.5rem 0.6rem;border:1.5px solid var(--border-color);border-radius:var(--radius-sm);font-size:0.68rem;font-weight:600;color:var(--text-primary);background:#FAFBFC;outline:none;cursor:pointer;">
                    <option value="">-- Select Category --</option>
                </select>

                <label style="font-size:0.58rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-top:0.2rem;">3. Select Name</label>
                <select id="exNameFilter" onchange="exSearchSelectedName()" style="width:100%;padding:0.5rem 0.6rem;border:1.5px solid var(--border-color);border-radius:var(--radius-sm);font-size:0.68rem;font-weight:600;color:var(--text-primary);background:#FAFBFC;outline:none;cursor:pointer;" disabled>
                    <option value="">-- Select Name --</option>
                </select>
            </div>

            <!-- EMPLOYEE DETAIL TABLE (below search) - DYNAMIC COLUMNS -->
            <div class="ex-detail-table-wrap" id="exDetailTableWrap" style="display:none;">
                <div class="ex-detail-table-header">
                    <span>📋 EMPLOYEE DATA</span>
                    <span id="exDetailCount" style="font-size:0.6rem;font-weight:600;color:rgba(255,255,255,0.7);">0 Records</span>
                </div>
                <div style="overflow-x:auto;max-height:280px;overflow-y:auto;">
                    <table class="ex-detail-table">
                        <thead id="exDetailHead">
                            <tr>
                                <th>DASHBOARD</th>
                                <th>CATEGORY</th>
                                <th>VALUE</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody id="exDetailBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Empty state when no search -->
            <div class="ex-empty-state" id="exEmptyState">
                <div class="empty-icon">🔍</div>
                <h3>Search Participant</h3>
                <p>Select Dashboard → Category → Name to view employee data, photo, and AI-generated performance analysis.</p>
            </div>
        </div>
    </div>

    <!-- ===== SMART ANALYTICS AI SECTION ===== -->
    <div class="ex-ai-section" id="exAiSection">
        <div class="ex-ai-header">
            <span>🤖 SMART ANALYTICS AI — Performance Findings & Recommendations</span>
            <span class="ai-badge">⚡ AI-POWERED</span>
        </div>
        <div class="ex-ai-body">
            <div class="ex-ai-metrics" id="exAiMetrics"></div>
            <div class="ex-ai-findings" id="exAiFindings">
                <div class="findings-title">📋 Performance Findings</div>
                <div class="findings-text" id="exAiFindingsText">Search for a participant to generate AI-powered performance analysis.</div>
            </div>
            <div class="ex-ai-recommendations" id="exAiRecommendations">
                <div class="rec-title">💡 Strategic Recommendations</div>
                <div class="rec-text" id="exAiRecText">AI recommendations will appear once a participant is selected.</div>
            </div>
        </div>
    </div>

</div>

<!-- ===== PHOTO EXPAND MODAL (with download) ===== -->
<div class="ex-photo-modal-overlay" id="exPhotoModalOverlay">
    <div class="ex-photo-modal">
        <div class="ex-photo-modal-header">
            <div class="pm-title">
                <span>📸 PARTICIPANT PLACEMENT</span>
            </div>
            <button class="pm-close-btn" onclick="exClosePhotoModal()">✕</button>
        </div>
        <div class="ex-photo-modal-body">
            <div id="exPhotoModalContent" style="width:100%;display:flex;justify-content:center;"></div>
        </div>
        <div class="ex-photo-modal-footer">
            <button onclick="exDownloadPhoto()">⬇️ Download Placement</button>
        </div>
    </div>
</div>

<!-- ===== EXPORT FORMAT SELECTION MODAL (JPEG/PNG) ===== -->
<div class="ex-export-modal-overlay" id="exExportModalOverlay" onclick="if(event.target===this)exCloseExportModal()">
    <div class="ex-export-modal">
        <div class="ex-export-modal-header">
            <div class="export-title">
                <span>📸 EXPORT PLACEMENT</span>
            </div>
            <button class="export-close-btn" onclick="exCloseExportModal()">✕</button>
        </div>
        <div class="ex-export-modal-body">
            <div style="font-size:0.7rem;color:#64748b;margin-bottom:0.85rem;font-weight:500;">
                Select format — the exported image will match the on-screen composition exactly.
            </div>
            <div class="ex-export-format-options">
                <div class="ex-export-format-option selected" id="exExportPngOption" onclick="exSelectExportFormat('png')">
                    <div class="format-icon png">PNG</div>
                    <div class="format-info">
                        <div class="format-name">PNG Image</div>
                        <div class="format-desc">High quality, transparent-capable, larger file size</div>
                    </div>
                    <div class="format-check">✓</div>
                </div>
                <div class="ex-export-format-option" id="exExportJpgOption" onclick="exSelectExportFormat('jpg')">
                    <div class="format-icon jpg">JPG</div>
                    <div class="format-info">
                        <div class="format-name">JPEG Image</div>
                        <div class="format-desc">Smaller file size, recommended for photos</div>
                    </div>
                    <div class="format-check">✓</div>
                </div>
            </div>
        </div>
        <div class="ex-export-modal-footer">
            <button class="ex-export-btn" id="exExportConfirmBtn" onclick="exConfirmExport()">⬇️ Export</button>
            <button class="ex-export-cancel-btn" onclick="exCloseExportModal()">Cancel</button>
        </div>
    </div>
</div>

<!-- ===== AI CONVERSATION POPUP MODAL (with blur overlay) ===== -->
<div class="ex-ai-modal-overlay" id="exAiModalOverlay">
    <div class="ex-ai-modal">
        <div class="ex-ai-modal-header">
            <div class="ai-title">
                <img src="/Ai4.PNG?v=20260805" alt="AI Analyst">
                <span>AI Regional Analyst</span>
            </div>
            <button class="ai-close-btn" onclick="exCloseAiModal()">✕</button>
        </div>
        <div class="ex-ai-modal-body">
            <div class="ex-ai-conversation" id="exAiConversation">
                <!-- Messages will be dynamically added here -->
            </div>
        </div>
        <div class="ex-ai-modal-footer">
            <input type="text" id="exAiChatInput" placeholder="Ask about regional performance..." onkeydown="if(event.key==='Enter')exSendAiMessage()">
            <button onclick="exSendAiMessage()">Send</button>
        </div>
    </div>
</div>

<script>
// ===== REAL-TIME GREETING =====
(function updateGreeting() {
    var now = new Date();
    var hour = now.getHours();
    var greeting = '';
    if (hour >= 5 && hour < 12) { greeting = 'Good morning! Welcome.'; }
    else if (hour >= 12 && hour < 18) { greeting = 'Good afternoon, Welcome!'; }
    else { greeting = 'Good evening, Welcome!'; }
    document.getElementById('greetingText').textContent = greeting;
    var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('greetingDate').textContent = now.toLocaleDateString('en-US', options);
    
    // Set year options
    var yearSelect = document.getElementById('exYear');
    if (yearSelect) {
        var cy = new Date().getFullYear();
        yearSelect.innerHTML = '';
        for (var y = cy - 2; y <= cy + 1; y++) {
            var opt = document.createElement('option');
            opt.value = y;
            opt.textContent = y;
            if (y === cy) opt.selected = true;
            yearSelect.appendChild(opt);
        }
    }
})();

// ===== FORMATTING HELPERS =====
function fmtPeso(v) { return '₱' + Number(v||0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}); }
function fmtNum(v) { return Number(v||0).toLocaleString(); }

// ===== LOAD INITIAL DATA =====

// Add ApexCharts CDN if not already loaded
if (typeof ApexCharts === 'undefined') {
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/apexcharts@3.45.2';
    document.head.appendChild(script);
}
var exAllData = null;

// Auto-detect the month with data and initialize
function detectAndLoadData() {
    var y = document.getElementById('exYear') ? document.getElementById('exYear').value : new Date().getFullYear();
    
    // Show loading
    showExecutiveLoading();
    
    // First check available months
    window.fetch('/executive/api/available-months?year=' + y)
        .then(function(r) { return r.json(); })
        .then(function(j) {
            var defaultMonth;
            if (j.success && j.data && j.data.available_months && j.data.available_months.length > 0) {
                // Use the latest available month
                defaultMonth = j.data.available_months[j.data.available_months.length - 1];
            } else {
                defaultMonth = new Date().getMonth() + 1;
            }
            
            // Set the month selector
            var monthSelect = document.getElementById('exMonth');
            if (monthSelect) monthSelect.value = defaultMonth;
            
            // Now load data with the correct month
            loadExecutiveData(defaultMonth, y);
        })
        .catch(function(e) {
            console.error('Auto-detect error:', e);
            loadExecutiveData(new Date().getMonth() + 1, y);
        });
}

function showExecutiveLoading() {
    var kpiRow = document.getElementById('exKpiRow');
    var loadingOverlay = document.getElementById('exKpiLoading');
    if (!loadingOverlay) {
        loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'exKpiLoading';
        loadingOverlay.style.cssText = 'grid-column:1/-1;display:flex;align-items:center;justify-content:center;padding:2rem;gap:0.5rem;position:absolute;inset:0;background:rgba(255,255,255,0.85);z-index:5;border-radius:var(--radius-lg);';
        loadingOverlay.innerHTML = '<div class="spinner"></div><span style="font-size:0.75rem;color:#94a3b8;">Loading data...</span>';
        kpiRow.style.position = 'relative';
        kpiRow.appendChild(loadingOverlay);
    } else {
        loadingOverlay.style.display = 'flex';
    }
}

function loadExecutiveData(month, year) {
    var m = month || document.getElementById('exMonth').value;
    var y = year || document.getElementById('exYear').value;
    
    showExecutiveLoading();
    
    window.fetch('/executive/api/all-data?month='+m+'&year='+y)
        .then(function(r){ return r.json(); })
        .then(function(j){
            // Hide loading overlay
            var overlay = document.getElementById('exKpiLoading');
            if (overlay) overlay.style.display = 'none';
            
            if (j.success && j.data) {
                exAllData = j.data;
                renderKpiData(j.data);
                renderTrendChart(j.data);
                renderRegionalChart(j.data);
                renderTopPerformers(j.data);
            }
        })
        .catch(function(e){ 
            console.error('Load error:', e);
            var overlay = document.getElementById('exKpiLoading');
            if (overlay) overlay.style.display = 'none';
        });
}

function renderKpiData(data) {
    var kpi = data.kpi || {};
    
    // ===== 1. MONTHLY REVENUE (Base: Sales Excellence Awardee Dashboard) =====
    document.getElementById('exMonthlyRevenue').textContent = fmtPeso(kpi.total_revenue || 0);
    
    // Revenue change indicator - comparative vs last month
    var revChange = kpi.revenue_change || 0;
    var changeEl = document.getElementById('exRevenueChange');
    changeEl.textContent = (revChange >= 0 ? '+' : '') + revChange.toFixed(1) + '% vs previous month';
    changeEl.style.color = revChange >= 0 ? '#059669' : '#dc2626';
    changeEl.style.fontWeight = '700';
    
    // ===== 2. AVS GROWTH (Base: Top Branch Recognition Dashboard - AVS Growth) =====
    var avsGrowth = kpi.avs_growth || 0;
    var avsEl = document.getElementById('exAvsGrowth');
    avsEl.textContent = avsGrowth.toFixed(2) + '%';
    avsEl.style.color = avsGrowth >= 0 ? '#059669' : '#dc2626';
    
    // AVS Growth change indicator - comparative vs last month
    var avsGrowthChange = kpi.avs_growth_change || 0;
    var avsSubEl = document.querySelector('#exKpiRow .ex-kpi-xs:nth-child(2) .kpi-sub');
    if (avsSubEl) {
        avsSubEl.textContent = (avsGrowthChange >= 0 ? '+' : '') + avsGrowthChange.toFixed(1) + '% vs previous month';
        avsSubEl.style.color = avsGrowthChange >= 0 ? '#059669' : '#dc2626';
        avsSubEl.style.fontWeight = '700';
    }
    
    // ===== 3. COMBINED VOLUME (Base: Sales Excellence Elite Circle Dashboard - Combined Volume) =====
    document.getElementById('exCombinedVolume').textContent = fmtNum(kpi.total_volume || 0);
    
    // Volume change indicator - comparative vs last month
    var volChange = kpi.volume_change || 0;
    var volSubEl = document.querySelector('#exKpiRow .ex-kpi-xs:nth-child(3) .kpi-sub');
    if (volSubEl) {
        volSubEl.textContent = (volChange >= 0 ? '+' : '') + volChange.toFixed(1) + '% vs previous month';
        volSubEl.style.color = volChange >= 0 ? '#059669' : '#dc2626';
        volSubEl.style.fontWeight = '700';
    }
    
    // ===== 4. COMBINED CM (Base: Sales Excellence Elite Circle Dashboard - Combined CM) =====
    document.getElementById('exCombinedCm').textContent = fmtPeso(kpi.total_cm || 0);
    
    // CM change indicator - comparative vs last month
    var cmChange = kpi.cm_change || 0;
    var cmSubEl = document.querySelector('#exKpiRow .ex-kpi-xs:nth-child(4) .kpi-sub');
    if (cmSubEl) {
        cmSubEl.textContent = (cmChange >= 0 ? '+' : '') + cmChange.toFixed(1) + '% vs previous month';
        cmSubEl.style.color = cmChange >= 0 ? '#059669' : '#dc2626';
        cmSubEl.style.fontWeight = '700';
    }
}

// ===== TREND CHART =====
var exTrendChart = null;

function renderTrendChart(data) {
    var trend = data.trend || { categories: [], series: [] };
    var el = document.getElementById('exTrendChart');
    el.innerHTML = '';
    
    if (exTrendChart) { exTrendChart.destroy(); exTrendChart = null; }
    
    if (!trend.categories || !trend.categories.length) {
        el.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#94a3b8;font-size:0.8rem;">No trend data available.</div>';
        return;
    }
    
    var colors = ['#0B7A3B', '#E31C23', '#2D6CDF', '#F59E0B'];
    var series = (trend.series || []).map(function(s, i) {
        return { name: s.name, data: s.data, color: colors[i % colors.length] };
    });
    
    exTrendChart = new ApexCharts(el, {
        chart: { type: 'line', height: '100%', fontFamily: 'inherit', toolbar: { show: true }, foreColor: '#64748b', dropShadow: { enabled: true, top: 2, left: 0, blur: 4, opacity: 0.1 } },
        series: series,
        xaxis: { categories: trend.categories, labels: { style: { fontSize: '10px', fontWeight: 600 } } },
        yaxis: {
            labels: { style: { fontSize: '10px' } },
        },
        colors: colors,
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 4, hover: { size: 7 } },
        grid: { borderColor: '#e2e8f0', strokeDashArray: 3 },
        legend: { position: 'top', fontSize: '11px', fontWeight: 600 },
        tooltip: {
            y: {
                formatter: function(v, { seriesIndex }) {
                    var name = (trend.series[seriesIndex] || {}).name || '';
                    if (name.indexOf('Revenue') !== -1) return fmtPeso(v);
                    if (name.indexOf('Growth') !== -1) return Number(v).toFixed(2) + '%';
                    if (name.indexOf('CM') !== -1) return fmtPeso(v);
                    return fmtNum(v);
                }
            }
        }
    });
    exTrendChart.render();
}

// ===== REGIONAL PERFORMANCE - MAP + STOCK INDICATOR =====
var exRegionalData = [];
var exRegionalCategory = 'revenue';

function renderRegionalChart(data) {
    exRegionalData = data.regional || [];
    exRenderRegionStockList();
}

// Switch the active category indicator
function exSetRegionalCategory(cat) {
    exRegionalCategory = cat;
    document.querySelectorAll('.ex-region-cat-btn').forEach(function(btn) {
        var isActive = btn.dataset.cat === cat;
        btn.classList.toggle('active', isActive);
        
        // Update button style based on active state
        var colors = {
            revenue: { border: '#0B7A3B', bg: '#0B7A3B', txt: '#fff' },
            growth:   { border: '#E31C23', bg: '#E31C23', txt: '#fff' },
            cm:       { border: '#F59E0B', bg: '#F59E0B', txt: '#fff' },
            volume:   { border: '#2D6CDF', bg: '#2D6CDF', txt: '#fff' }
        };
        var c = colors[btn.dataset.cat] || { border: '#0B7A3B', bg: '#0B7A3B', txt: '#fff' };
        if (isActive) {
            btn.style.background = c.border;
            btn.style.color = '#fff';
        } else {
            btn.style.background = '#fff';
            btn.style.color = c.border;
        }
    });
    
    // ===== SWITCH AI MODEL BASED ON CATEGORY =====
    exSwitchAiModel(cat);
    
    exRenderRegionStockList();
}

// ===== AI MODEL SWITCHING - Switches per category: Ai1=Revenue, Ai2=Growth, Ai3=CM, Ai4=Volume =====
function exSwitchAiModel(cat) {
    var imgEl = document.getElementById('exAiModelImg');
    if (!imgEl) return;
    
    // Map each category to its specific AI model image
    var categoryToImage = {
        revenue: '/Ai1.png?v=20260805',  // Revenue indicator
        growth:   '/Ai2.png?v=20260805',  // Growth% indicator
        cm:       '/Ai3.png?v=20260805',  // CM indicator
        volume:   '/Ai4.png?v=20260805'   // Volume indicator
    };
    var imgSrc = categoryToImage[cat] || '/Ai1.png?v=20260805';
    var currentSrc = imgEl.getAttribute('src') || '';
    if (currentSrc !== imgSrc) {
        imgEl.style.transition = 'opacity 0.3s ease';
        imgEl.style.opacity = '0.3';
        setTimeout(function() {
            imgEl.setAttribute('src', imgSrc);
            imgEl.style.opacity = '1';
        }, 300);
    }
}

// ===== AI CHARACTER DRAG/CLICK - Generates report when dragged or clicked =====
var exAiDragStart = null;
var exAiDragged = false;

function initAiCharacterDrag() {
    var charEl = document.getElementById('exAiCharacter');
    if (!charEl) return;
    
    // Use pointer events for better cross-browser support
    charEl.addEventListener('pointerdown', function(e) {
        exAiDragStart = { x: e.clientX, y: e.clientY };
        exAiDragged = false;
        charEl.style.cursor = 'grabbing';
        charEl.style.transform = 'scale(1.05)';
        charEl.style.transition = 'transform 0.15s ease';
        e.preventDefault();
    });
    
    document.addEventListener('pointermove', function(e) {
        if (!exAiDragStart) return;
        var dx = Math.abs(e.clientX - exAiDragStart.x);
        var dy = Math.abs(e.clientY - exAiDragStart.y);
        if (dx > 3 || dy > 3) {
            exAiDragged = true;
        }
    });
    
    document.addEventListener('pointerup', function(e) {
        if (!exAiDragStart) return;
        charEl.style.cursor = 'grab';
        charEl.style.transform = 'scale(1)';
        
        // Open AI conversation modal on drag OR simple click
        if (exAiDragged || true) {
            exOpenAiModal();
        }
        
        exAiDragStart = null;
        exAiDragged = false;
    });
    
    // Also support touch events for mobile
    charEl.addEventListener('touchstart', function(e) {
        var touch = e.touches[0];
        exAiDragStart = { x: touch.clientX, y: touch.clientY };
        exAiDragged = false;
        charEl.style.transform = 'scale(1.05)';
    });
    
    document.addEventListener('touchend', function(e) {
        if (!exAiDragStart) return;
        charEl.style.transform = 'scale(1)';
        exOpenAiModal();
        exAiDragStart = null;
        exAiDragged = false;
    });
}

// ===== PHOTO EXPAND - ENLARGE IN PLACE =====
// The photo square stays in the DOM, just scaled up with CSS
// This guarantees pixel-perfect identical rendering

function exOpenPhotoModal() {
    var photoSquare = document.getElementById('exPhotoSquare');
    if (!photoSquare) return;
    
    // Add expanded class - CSS handles the enlargement
    photoSquare.classList.add('expanded');
    photoSquare.classList.remove('clickable');
    photoSquare.onclick = null;
    
    // Show overlay, close button, and download button
    var overlay = document.getElementById('exPhotoExpandOverlay');
    var closeBtn = document.getElementById('exPhotoCloseBtn');
    var downloadBtn = document.getElementById('exPhotoDownloadBtn');
    
    if (overlay) overlay.classList.add('show');
    if (closeBtn) closeBtn.classList.add('show');
    if (downloadBtn) downloadBtn.classList.add('show');
}

function exClosePhotoModal() {
    var photoSquare = document.getElementById('exPhotoSquare');
    if (!photoSquare) return;
    
    // Remove expanded class - restores original position
    photoSquare.classList.remove('expanded');
    
    // Hide overlay, close button, and download button
    var overlay = document.getElementById('exPhotoExpandOverlay');
    var closeBtn = document.getElementById('exPhotoCloseBtn');
    var downloadBtn = document.getElementById('exPhotoDownloadBtn');
    
    if (overlay) overlay.classList.remove('show');
    if (closeBtn) closeBtn.classList.remove('show');
    if (downloadBtn) downloadBtn.classList.remove('show');
    
    // Re-add clickable state
    photoSquare.classList.add('clickable');
    photoSquare.title = 'Click to expand placement';
    photoSquare.onclick = function(e) {
        if (photoSquare.classList.contains('canva-mode')) return;
        exOpenPhotoModal();
    };
}

// ===== EXPORT AS JPEG/PNG - PIXEL-PERFECT COMPOSITION =====
// Uses html2canvas to capture the actual photo square DOM element
// This preserves the EXACT on-screen composition: background, photo,
// Name, Position-Area, and Category - nothing changes when exported.

var exExportFormat = 'png';

// Open the export format selection modal
function exDownloadPhoto() {
    var photoSquare = document.getElementById('exPhotoSquare');
    if (!photoSquare) return;
    
    // Default to PNG
    exSelectExportFormat('png');
    
    // Show the export format modal
    var overlay = document.getElementById('exExportModalOverlay');
    if (overlay) overlay.classList.add('show');
}

// Select export format
function exSelectExportFormat(format) {
    exExportFormat = format;
    var pngOpt = document.getElementById('exExportPngOption');
    var jpgOpt = document.getElementById('exExportJpgOption');
    if (pngOpt) pngOpt.classList.toggle('selected', format === 'png');
    if (jpgOpt) jpgOpt.classList.toggle('selected', format === 'jpg');
}

// Close the export format modal
function exCloseExportModal() {
    var overlay = document.getElementById('exExportModalOverlay');
    if (overlay) overlay.classList.remove('show');
}

// Confirm and export — uses native Canvas API for pixel-perfect rendering
// Reads exact pixel positions from getBoundingClientRect() to guarantee
// the exported image matches the on-screen composition exactly.
function exConfirmExport() {
    var photoSquare = document.getElementById('exPhotoSquare');
    if (!photoSquare) return;
    
    // Close the format modal
    exCloseExportModal();
    
    // Disable the download button and show loading
    var btn = document.getElementById('exPhotoDownloadBtn');
    if (btn) {
        btn.innerHTML = '⏳ Exporting...';
        btn.disabled = true;
    }
    
    // Get the participant name for the filename
    var nameEl = document.getElementById('exPhotoName');
    var participantName = nameEl ? nameEl.textContent.trim() : 'participant';
    if (!participantName || participantName === '—') participantName = 'participant';
    participantName = participantName.replace(/[^a-zA-Z0-9-_ ]/g, '').replace(/\s+/g, '_');
    
    var ext = (exExportFormat === 'jpg') ? 'jpg' : 'png';
    var mimeType = (exExportFormat === 'jpg') ? 'image/jpeg' : 'image/png';
    var fileName = participantName + '_placement.' + ext;
    
    // ===== FIX: Temporarily remove .expanded class to get ORIGINAL (non-scaled) dimensions =====
    // The .expanded class applies scale(1.35) which makes getBoundingClientRect()
    // return scaled dimensions, but getComputedStyle() returns unscaled font sizes.
    // This mismatch causes the text to appear smaller than the background.
    var wasExpanded = photoSquare.classList.contains('expanded');
    var originalInlineTransition = photoSquare.style.transition;
    if (wasExpanded) {
        // Disable transitions to prevent measurement during CSS transition
        photoSquare.style.transition = 'none';
        photoSquare.classList.remove('expanded');
        // Force reflow so the browser applies the new styles immediately
        void photoSquare.offsetWidth;
    }
    
    // Get the actual rendered dimensions of the photo square
    var squareRect = photoSquare.getBoundingClientRect();
    var W = Math.round(squareRect.width);
    var H = Math.round(squareRect.height);
    
    // Get all element data
    var nameEl2 = document.getElementById('exPhotoName');
    var posEl2 = document.getElementById('exPhotoPosition');
    var catEl2 = document.getElementById('exPhotoCategory');
    var photoEl2 = document.getElementById('exPhotoPlaceholder');
    var photoImg = photoEl2 ? photoEl2.querySelector('img') : null;
    
    var nameText = nameEl2 ? nameEl2.textContent : '—';
    var positionText = posEl2 ? posEl2.textContent : '—';
    var categoryText = catEl2 ? catEl2.textContent : '—';
    
    // Get EXACT pixel positions relative to the photo square using getBoundingClientRect
    function getElRect(el) {
        if (!el) return { x: 0, y: 0, w: 0, h: 0 };
        var r = el.getBoundingClientRect();
        return {
            x: r.left - squareRect.left,
            y: r.top - squareRect.top,
            w: r.width,
            h: r.height
        };
    }
    
    var nameRect = getElRect(nameEl2);
    var posRect = getElRect(posEl2);
    var catRect = getElRect(catEl2);
    var photoRect = getElRect(photoEl2);
    
    // Get exact font sizes from computed styles (in px)
    var nameFs = nameEl2 ? parseFloat(getComputedStyle(nameEl2).fontSize) : 44.8;
    var posFs = posEl2 ? parseFloat(getComputedStyle(posEl2).fontSize) : 10.56;
    var catFs = catEl2 ? parseFloat(getComputedStyle(catEl2).fontSize) : 10.88;
    
    // Get exact colors
    var nameColor = nameEl2 ? getComputedStyle(nameEl2).color : '#0B7A3B';
    var posColor = posEl2 ? getComputedStyle(posEl2).color : '#6B7280';
    var catColor = catEl2 ? getComputedStyle(catEl2).color : '#FFFFFF';
    
    // ===== FIX: Re-add .expanded class to restore UI state =====
    // Restore the expanded state so the on-screen UI doesn't change
    if (wasExpanded) {
        photoSquare.classList.add('expanded');
        photoSquare.style.transition = originalInlineTransition;
    }
    
    // Determine background image
    var bgImage = '/Image section bg.png';
    if (photoSquare.classList.contains('rank-1st')) bgImage = '/1st.png';
    else if (photoSquare.classList.contains('rank-2nd')) bgImage = '/2nd.png';
    else if (photoSquare.classList.contains('rank-3rd')) bgImage = '/3rd.png';
    else if (photoSquare.classList.contains('rank-grand-slam')) bgImage = '/1st.png';
    
    // Load background image
    var bg = new Image();
    bg.crossOrigin = 'anonymous';
    
    function drawExport() {
        // Use 2x scale for high-resolution export
        // Multiply all font sizes and positions by 2 so text appears
        // the SAME relative size as on screen, but at 2x resolution
        var SCALE = 2;
        var canvas = document.createElement('canvas');
        canvas.width = W * SCALE;
        canvas.height = H * SCALE;
        var ctx = canvas.getContext('2d');
        ctx.scale(SCALE, SCALE);
        
        // Draw background image (cover fit)
        if (bg.complete && bg.naturalWidth > 0) {
            // Calculate cover-fit dimensions
            var bgRatio = bg.naturalWidth / bg.naturalHeight;
            var targetRatio = W / H;
            var drawW, drawH, drawX, drawY;
            
            if (bgRatio > targetRatio) {
                drawH = H;
                drawW = H * bgRatio;
                drawX = (W - drawW) / 2;
                drawY = 0;
            } else {
                drawW = W;
                drawH = W / bgRatio;
                drawX = 0;
                drawY = (H - drawH) / 2;
            }
            ctx.drawImage(bg, drawX, drawY, drawW, drawH);
        } else {
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, W, H);
        }
        
        // Draw decorative radial gradients (mimic ::before and ::after)
        var grad1 = ctx.createRadialGradient(W * 1.2, -H * 0.3, 0, W * 1.2, -H * 0.3, 250);
        grad1.addColorStop(0, 'rgba(255,255,255,0.06)');
        grad1.addColorStop(1, 'rgba(255,255,255,0)');
        ctx.fillStyle = grad1;
        ctx.fillRect(0, 0, W, H);
        
        var grad2 = ctx.createRadialGradient(-W * 0.2, H * 1.2, 0, -W * 0.2, H * 1.2, 200);
        grad2.addColorStop(0, 'rgba(255,255,255,0.04)');
        grad2.addColorStop(1, 'rgba(255,255,255,0)');
        ctx.fillStyle = grad2;
        ctx.fillRect(0, 0, W, H);
        
        // Draw participant photo at its EXACT pixel position
        if (photoImg && photoRect.w > 0 && photoRect.h > 0) {
            var pImg = new Image();
            pImg.crossOrigin = 'anonymous';
            pImg.onload = function() {
                // Draw photo with rounded corners
                ctx.save();
                ctx.beginPath();
                var radius = 12;
                var px = photoRect.x, py = photoRect.y, pw = photoRect.w, ph = photoRect.h;
                ctx.moveTo(px + radius, py);
                ctx.lineTo(px + pw - radius, py);
                ctx.quadraticCurveTo(px + pw, py, px + pw, py + radius);
                ctx.lineTo(px + pw, py + ph - radius);
                ctx.quadraticCurveTo(px + pw, py + ph, px + pw - radius, py + ph);
                ctx.lineTo(px + radius, py + ph);
                ctx.quadraticCurveTo(px, py + ph, px, py + ph - radius);
                ctx.lineTo(px, py + radius);
                ctx.quadraticCurveTo(px, py, px + radius, py);
                ctx.closePath();
                ctx.clip();
                
                // Draw image with contain fit
                var imgRatio = pImg.naturalWidth / pImg.naturalHeight;
                var boxRatio = pw / ph;
                var drawW2, drawH2, drawX2, drawY2;
                if (imgRatio > boxRatio) {
                    drawW2 = pw;
                    drawH2 = pw / imgRatio;
                    drawX2 = px;
                    drawY2 = py + (ph - drawH2) / 2;
                } else {
                    drawH2 = ph;
                    drawW2 = ph * imgRatio;
                    drawX2 = px + (pw - drawW2) / 2;
                    drawY2 = py;
                }
                ctx.drawImage(pImg, drawX2, drawY2, drawW2, drawH2);
                ctx.restore();
                
                // Draw text elements after photo
                drawTextElements();
            };
            pImg.onerror = function() {
                drawTextElements();
            };
            pImg.src = photoImg.src;
        } else {
            // No photo - draw initial letter
            if (photoRect.w > 0 && photoRect.h > 0) {
                ctx.fillStyle = 'rgba(255,255,255,0.6)';
                ctx.font = 'bold 48px Arial, sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(nameText.charAt(0).toUpperCase(), photoRect.x + photoRect.w / 2, photoRect.y + photoRect.h / 2);
            }
            drawTextElements();
        }
        
        function drawTextElements() {
            // Draw name at its EXACT pixel position (centered)
            ctx.textAlign = 'center';
            ctx.textBaseline = 'top';
            
            // Name - centered at 50% (translateX(-50%))
            var nameX = nameRect.x + (nameRect.w / 2);
            var nameY = nameRect.y;
            ctx.fillStyle = nameColor;
            ctx.font = '900 ' + nameFs + 'px Arial, sans-serif';
            ctx.shadowColor = 'rgba(0,0,0,0.2)';
            ctx.shadowBlur = 4;
            ctx.shadowOffsetY = 2;
            ctx.fillText(nameText, nameX, nameY);
            ctx.shadowBlur = 0;
            ctx.shadowOffsetY = 0;
            
            // Position - Area at its EXACT pixel position
            var posX = posRect.x + (posRect.w / 2);
            var posY = posRect.y;
            ctx.fillStyle = posColor;
            ctx.font = '700 ' + posFs + 'px Arial, sans-serif';
            ctx.fillText(positionText, posX, posY);
            
            // Category at its EXACT pixel position
            var catX = catRect.x + (catRect.w / 2);
            var catY = catRect.y;
            ctx.fillStyle = catColor;
            ctx.font = '700 ' + catFs + 'px Arial, sans-serif';
            ctx.fillText(categoryText, catX, catY);
            
            // Draw watermark
            ctx.fillStyle = 'rgba(255,255,255,0.2)';
            ctx.font = 'bold 7px Arial, sans-serif';
            ctx.textAlign = 'right';
            ctx.fillText('PHINMA UGC', W - 15, H - 10);
            
            // Download
            var link = document.createElement('a');
            link.download = fileName;
            link.href = canvas.toDataURL(mimeType, exExportFormat === 'jpg' ? 0.92 : undefined);
            link.click();
            
            // Restore button state
            if (btn) {
                btn.innerHTML = '⬇️ Download Placement';
                btn.disabled = false;
            }
        }
    }
    
    bg.onload = drawExport;
    bg.onerror = drawExport;
    bg.src = bgImage;
}

// ===== AI CONVERSATION MODAL =====
function exOpenAiModal() {
    var overlay = document.getElementById('exAiModalOverlay');
    if (!overlay) return;
    
    // Clear previous conversation and add welcome message
    var conv = document.getElementById('exAiConversation');
    conv.innerHTML = '';
    
    // Add AI welcome message
    exAddAiMessage('Hello! I\'m your AI Regional Analyst. I can provide insights on regional performance across Revenue, Growth, CM, and Volume. What would you like to know?');
    
    // Show the modal with blur
    overlay.classList.add('show');
    
    // Focus the input
    setTimeout(function() {
        var input = document.getElementById('exAiChatInput');
        if (input) input.focus();
    }, 300);
}

function exCloseAiModal() {
    var overlay = document.getElementById('exAiModalOverlay');
    if (!overlay) return;
    overlay.classList.remove('show');
}

// Close modal when clicking outside it
document.addEventListener('DOMContentLoaded', function() {
    var overlay = document.getElementById('exAiModalOverlay');
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                exCloseAiModal();
            }
        });
    }
    
    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            exCloseAiModal();
        }
    });
});

// Add a message to the conversation
function exAddAiMessage(text, isUser) {
    var conv = document.getElementById('exAiConversation');
    if (!conv) return;
    
    var now = new Date();
    var timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
    
    var msgDiv = document.createElement('div');
    msgDiv.className = 'ex-ai-msg ' + (isUser ? 'user' : 'ai');
    
    var avatarHtml = isUser 
        ? '<div class="msg-avatar">👤</div>'
        : '<div class="msg-avatar"><img src="/Ai4.PNG?v=20260805" alt="AI"></div>';
    
    msgDiv.innerHTML = avatarHtml + 
        '<div class="msg-bubble">' + text + 
        '<span class="msg-time">' + timeStr + '</span></div>';
    
    conv.appendChild(msgDiv);
    
    // Scroll to bottom
    var body = document.querySelector('.ex-ai-modal-body');
    if (body) body.scrollTop = body.scrollHeight;
}

// Show typing indicator
function exShowTyping() {
    var conv = document.getElementById('exAiConversation');
    if (!conv) return;
    
    var typingDiv = document.createElement('div');
    typingDiv.className = 'ex-ai-msg ai';
    typingDiv.id = 'exAiTyping';
    typingDiv.innerHTML = '<div class="msg-avatar"><img src="/Ai4.PNG?v=20260805" alt="AI"></div>' +
        '<div class="msg-bubble"><div class="ex-ai-typing"><span></span><span></span><span></span></div></div>';
    
    conv.appendChild(typingDiv);
    
    var body = document.querySelector('.ex-ai-modal-body');
    if (body) body.scrollTop = body.scrollHeight;
}

// Remove typing indicator
function exHideTyping() {
    var typing = document.getElementById('exAiTyping');
    if (typing) typing.remove();
}

// Send a message from the user
function exSendAiMessage() {
    var input = document.getElementById('exAiChatInput');
    if (!input) return;
    
    var text = input.value.trim();
    if (!text) return;
    
    // Add user message - use unicode escapes to safely escape HTML
    exAddAiMessage(text.replace(/\x26/g, '\x26amp;').replace(/</g, '\x26lt;').replace(/>/g, '\x26gt;'), true);
    input.value = '';
    
    // Show typing indicator
    exShowTyping();
    
    // Generate AI response after a delay
    setTimeout(function() {
        exHideTyping();
        var response = exGenerateAiResponse(text);
        exAddAiMessage(response, false);
    }, 800);
}

// Generate AI response based on user question
function exGenerateAiResponse(question) {
    var q = question.toLowerCase();
    
    // Check if asking about regional report
    if (q.indexOf('report') !== -1 || q.indexOf('summary') !== -1 || q.indexOf('overview') !== -1 || q.indexOf('analy') !== -1) {
        return exBuildAiRegionalReport();
    }
    
    // Check if asking about top region
    if (q.indexOf('top') !== -1 || q.indexOf('best') !== -1 || q.indexOf('leading') !== -1 || q.indexOf('highest') !== -1) {
        if (!exRegionalData.length) return 'No regional data available for analysis.';
        var sorted = exRegionalData.slice().sort(function(a, b) {
            return exGetRegionMetric(b).current - exGetRegionMetric(a).current;
        });
        var top = sorted[0];
        var m = exGetRegionMetric(top);
        return '🏆 <strong>' + (top.region || 'N/A') + '</strong> is currently the top performing region with <strong>' + exFormatRegionalValue(m.current) + '</strong> in ' + exRegionalCategory.toUpperCase() + '.';
    }
    
    // Check if asking about bottom/weakest region
    if (q.indexOf('bottom') !== -1 || q.indexOf('weak') !== -1 || q.indexOf('worst') !== -1 || q.indexOf('lowest') !== -1 || q.indexOf('attention') !== -1) {
        if (!exRegionalData.length) return 'No regional data available for analysis.';
        var sorted2 = exRegionalData.slice().sort(function(a, b) {
            return exGetRegionMetric(b).current - exGetRegionMetric(a).current;
        });
        var bottom = sorted2[sorted2.length - 1];
        var m2 = exGetRegionMetric(bottom);
        return '⚠️ <strong>' + (bottom.region || 'N/A') + '</strong> needs attention with <strong>' + exFormatRegionalValue(m2.current) + '</strong> in ' + exRegionalCategory.toUpperCase() + '.';
    }
    
    // Check if asking about revenue
    if (q.indexOf('revenue') !== -1 || q.indexOf('sales') !== -1) {
        if (!exRegionalData.length) return 'No regional data available for analysis.';
        var totalRev = 0;
        exRegionalData.forEach(function(r) { totalRev += Number(r.revenue || 0); });
        return '💰 Total <strong>Revenue</strong> across all regions: <strong>' + fmtPeso(totalRev) + '</strong>. Switch to the REVENUE indicator for detailed regional breakdown.';
    }
    
    // Check if asking about growth
    if (q.indexOf('growth') !== -1 || q.indexOf('increase') !== -1) {
        if (!exRegionalData.length) return 'No regional data available for analysis.';
        var totalGrowth = 0;
        var count = 0;
        exRegionalData.forEach(function(r) {
            if (r.growth) { totalGrowth += Number(r.growth); count++; }
        });
        var avgGrowth = count > 0 ? totalGrowth / count : 0;
        return '📈 Average <strong>Growth</strong> across regions: <strong>' + Number(avgGrowth).toFixed(2) + '%</strong>. ' + (avgGrowth >= 0 ? 'Positive momentum overall.' : 'Some regions may need attention.');
    }
    
    // Check if asking about CM
    if (q.indexOf('cm') !== -1 || q.indexOf('margin') !== -1 || q.indexOf('contribution') !== -1) {
        if (!exRegionalData.length) return 'No regional data available for analysis.';
        var totalCm = 0;
        exRegionalData.forEach(function(r) { totalCm += Number(r.cm || 0); });
        return '💵 Total <strong>Contribution Margin</strong> across all regions: <strong>' + fmtPeso(totalCm) + '</strong>. Switch to the CM indicator for detailed regional breakdown.';
    }
    
    // Check if asking about volume
    if (q.indexOf('volume') !== -1 || q.indexOf('quantity') !== -1) {
        if (!exRegionalData.length) return 'No regional data available for analysis.';
        var totalVol = 0;
        exRegionalData.forEach(function(r) { totalVol += Number(r.volume || 0); });
        return '📦 Total <strong>Volume</strong> across all regions: <strong>' + fmtNum(totalVol) + '</strong>. Switch to the VOLUME indicator for detailed regional breakdown.';
    }
    
    // Check if asking about a specific region
    var regionNames = ['south luzon', 'north', 'central luzon', 'visayas', 'mindanao', 'luzon'];
    for (var i = 0; i < regionNames.length; i++) {
        if (q.indexOf(regionNames[i]) !== -1) {
            if (!exRegionalData.length) return 'No regional data available for analysis.';
            var matchRegion = null;
            exRegionalData.forEach(function(r) {
                var regionLower = (r.region || '').toLowerCase();
                if (regionLower.indexOf(regionNames[i]) !== -1) matchRegion = r;
            });
            if (matchRegion) {
                var rm = exGetRegionMetric(matchRegion);
                var arrow = rm.change >= 0 ? '▲' : '▼';
                var arrowColor = rm.change >= 0 ? '#059669' : '#dc2626';
                return '📍 <strong>' + (matchRegion.region || 'N/A') + '</strong>: <strong>' + exFormatRegionalValue(rm.current) + '</strong> in ' + exRegionalCategory.toUpperCase() + ' <span style="color:' + arrowColor + ';">' + arrow + ' ' + (rm.change >= 0 ? '+' : '') + Number(rm.change).toFixed(1) + '%</span> vs last month.';
            }
        }
    }
    
    // Check if asking about recommendations
    if (q.indexOf('recommend') !== -1 || q.indexOf('suggest') !== -1 || q.indexOf('advice') !== -1 || q.indexOf('action') !== -1) {
        return exBuildAiRegionalReport();
    }
    
    // Check if asking about regions list
    if (q.indexOf('region') !== -1 || q.indexOf('list') !== -1 || q.indexOf('all') !== -1) {
        if (!exRegionalData.length) return 'No regional data available for analysis.';
        var regionList = '<strong>📍 Regions being analyzed:</strong><br>';
        exRegionalData.forEach(function(r, i) {
            var rm = exGetRegionMetric(r);
            regionList += (i + 1) + '. <strong>' + (r.region || 'N/A') + '</strong> — ' + exFormatRegionalValue(rm.current) + '<br>';
        });
        return regionList;
    }
    
    // Default response
    return 'I can help you analyze regional performance. Try asking about:<br>' +
        '• 📊 <strong>Regional report</strong> - Get a full performance analysis<br>' +
        '• 🏆 <strong>Top region</strong> - Find the best performing region<br>' +
        '• ⚠️ <strong>Bottom region</strong> - Identify areas needing attention<br>' +
        '• 💰 <strong>Revenue</strong>, 📈 <strong>Growth</strong>, 💵 <strong>CM</strong>, or 📦 <strong>Volume</strong><br>' +
        '• 📍 Specific regions like <strong>South Luzon</strong>, <strong>Visayas</strong>, etc.';
}

// Build the AI regional report based on current data and category
function exBuildAiRegionalReport() {
    if (!exRegionalData.length) {
        return '<div style="color:#94a3b8;">No regional data available for analysis.</div>';
    }
    
    var cat = exRegionalCategory;
    var catLabel = cat.toUpperCase();
    var catColors = {
        revenue: { accent: '#0B7A3B', label: 'REVENUE' },
        growth:  { accent: '#E31C23', label: 'GROWTH %' },
        cm:      { accent: '#F59E0B', label: 'CM' },
        volume:  { accent: '#2D6CDF', label: 'VOLUME' }
    };
    var cc = catColors[cat] || catColors.revenue;
    
    // Sort regions by current metric value (descending)
    var sorted = exRegionalData.slice().sort(function(a, b) {
        return exGetRegionMetric(b).current - exGetRegionMetric(a).current;
    });
    
    var topRegion = sorted[0];
    var bottomRegion = sorted[sorted.length - 1];
    var topMetric = exGetRegionMetric(topRegion);
    var bottomMetric = exGetRegionMetric(bottomRegion);
    
    // Calculate total across all regions
    var total = 0;
    sorted.forEach(function(r) {
        total += exGetRegionMetric(r).current;
    });
    
    // Count positive/negative changes
    var positiveCount = 0;
    var negativeCount = 0;
    sorted.forEach(function(r) {
        var m = exGetRegionMetric(r);
        if (m.change > 0) positiveCount++;
        else if (m.change < 0) negativeCount++;
    });
    
    var html = '';
    
    // ===== 1. OVERVIEW =====
    html += '<div style="margin-bottom:0.5rem;">';
    html += '<strong style="color:' + cc.accent + ';">📊 ' + catLabel + ' PERFORMANCE OVERVIEW</strong><br>';
    html += 'Analyzing <strong>' + sorted.length + ' regions</strong> for ' + catLabel + ' performance. ';
    html += 'Total ' + catLabel.toLowerCase() + ': <strong>' + exFormatRegionalValue(total) + '</strong> across all regions.';
    html += '</div>';
    
    // ===== 2. TOP PERFORMER =====
    html += '<div style="margin-bottom:0.5rem;padding:0.4rem 0.5rem;background:#f0fdf4;border-left:3px solid #059669;border-radius:4px;">';
    html += '🏆 <strong>Top Region:</strong> <strong>' + (topRegion.region || 'N/A') + '</strong> leads with <strong>' + exFormatRegionalValue(topMetric.current) + '</strong>';
    if (topMetric.change >= 0) {
        html += ' <span style="color:#059669;">(+' + Number(topMetric.change).toFixed(1) + '% vs last month)</span>';
    } else {
        html += ' <span style="color:#dc2626;">(' + Number(topMetric.change).toFixed(1) + '% vs last month)</span>';
    }
    html += '</div>';
    
    // ===== 3. BOTTOM PERFORMER =====
    html += '<div style="margin-bottom:0.5rem;padding:0.4rem 0.5rem;background:#fef2f2;border-left:3px solid #dc2626;border-radius:4px;">';
    html += '⚠️ <strong>Needs Attention:</strong> <strong>' + (bottomRegion.region || 'N/A') + '</strong> is at <strong>' + exFormatRegionalValue(bottomMetric.current) + '</strong>';
    if (bottomMetric.change < 0) {
        html += ' <span style="color:#dc2626;">(' + Number(bottomMetric.change).toFixed(1) + '% decline)</span>';
    }
    html += '</div>';
    
    // ===== 4. TREND ANALYSIS =====
    html += '<div style="margin-bottom:0.5rem;">';
    html += '📈 <strong>Trend Analysis:</strong> ';
    if (positiveCount > negativeCount) {
        html += '<span style="color:#059669;">' + positiveCount + ' of ' + sorted.length + ' regions</span> show positive growth momentum.';
    } else if (negativeCount > positiveCount) {
        html += '<span style="color:#dc2626;">' + negativeCount + ' of ' + sorted.length + ' regions</span> are experiencing decline.';
    } else {
        html += 'Balanced performance with ' + positiveCount + ' regions growing and ' + negativeCount + ' declining.';
    }
    html += '</div>';
    
    // ===== 5. REGION-BY-REGION BREAKDOWN =====
    html += '<div style="margin-bottom:0.5rem;">';
    html += '<strong>📍 Region Breakdown:</strong><br>';
    sorted.forEach(function(r, i) {
        var m = exGetRegionMetric(r);
        var isUp = m.change >= 0;
        var arrow = isUp ? '▲' : '▼';
        var arrowColor = isUp ? '#059669' : '#dc2626';
        var pct = total > 0 ? ((m.current / total) * 100).toFixed(1) : '0.0';
        
        html += '<div style="display:flex;align-items:center;gap:0.4rem;padding:0.2rem 0;font-size:0.68rem;">';
        html += '<span style="width:18px;height:18px;border-radius:50%;background:' + (i === 0 ? '#059669' : (i === sorted.length - 1 ? '#dc2626' : '#94a3b8')) + ';color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:0.55rem;font-weight:800;flex-shrink:0;">' + (i + 1) + '</span>';
        html += '<span style="flex:1;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + (r.region || 'N/A') + '</span>';
        html += '<span style="font-weight:700;color:#1e293b;">' + exFormatRegionalValue(m.current) + '</span>';
        html += '<span style="color:' + arrowColor + ';font-weight:700;font-size:0.6rem;width:55px;text-align:right;">' + arrow + ' ' + (isUp ? '+' : '') + Number(m.change).toFixed(1) + '%</span>';
        html += '<span style="color:#94a3b8;font-size:0.6rem;width:35px;text-align:right;">' + pct + '%</span>';
        html += '</div>';
    });
    html += '</div>';
    
    // ===== 6. AI RECOMMENDATIONS =====
    html += '<div style="margin-top:0.5rem;padding:0.5rem;background:#f0f7ff;border-left:3px solid #2D6CDF;border-radius:4px;">';
    html += '<strong style="color:#1e40af;">💡 AI Recommendations:</strong><br>';
    
    var recs = [];
    if (topMetric.change > 0) {
        recs.push('Leverage <strong>' + (topRegion.region || 'N/A') + '</strong>\'s momentum (' + Number(topMetric.change).toFixed(1) + '% growth) as a benchmark for other regions.');
    }
    if (bottomMetric.change < 0) {
        recs.push('Investigate the decline in <strong>' + (bottomRegion.region || 'N/A') + '</strong> (' + Number(bottomMetric.change).toFixed(1) + '%) and implement corrective action plans.');
    }
    if (positiveCount === sorted.length) {
        recs.push('All regions are growing — consider increasing targets to maximize overall performance.');
    } else if (negativeCount === sorted.length) {
        recs.push('All regions are declining — conduct a comprehensive market review and adjust strategies immediately.');
    }
    if (sorted.length > 1) {
        var gap = topMetric.current > 0 ? ((topMetric.current - bottomMetric.current) / topMetric.current * 100).toFixed(1) : 0;
        recs.push('The performance gap between top and bottom regions is <strong>' + gap + '%</strong> — focus on knowledge sharing and best practice transfer.');
    }
    recs.push('Schedule a regional performance review to discuss ' + catLabel + ' targets and align strategies for the next period.');
    
    recs.forEach(function(rec, i) {
        html += '<div style="display:flex;align-items:flex-start;gap:0.3rem;padding:0.15rem 0;font-size:0.68rem;">';
        html += '<span style="color:#2D6CDF;font-weight:800;flex-shrink:0;">' + (i + 1) + '.</span>';
        html += '<span>' + rec + '</span>';
        html += '</div>';
    });
    html += '</div>';
    
    return html;
}

// Format value based on current category
function exFormatRegionalValue(val) {
    val = Number(val || 0);
    if (exRegionalCategory === 'growth') {
        return Number(val).toFixed(2) + '%';
    }
    if (exRegionalCategory === 'cm') {
        return fmtPeso(val);
    }
    if (exRegionalCategory === 'revenue') {
        return fmtPeso(val);
    }
    // volume
    return fmtNum(val);
}

// Calculate % change between current and previous
function exCalcRegionalChange(current, prev) {
    current = Number(current || 0);
    prev = Number(prev || 0);
    if (prev > 0) {
        return ((current - prev) / prev) * 100;
    }
    if (current > 0) {
        return 100.0;
    }
    return 0;
}

// Get current value, previous value, and change for a region based on selected category
function exGetRegionMetric(region) {
    var r = region || {};
    var cur = 0, prev = 0;
    
    if (exRegionalCategory === 'revenue') {
        cur = Number(r.revenue || 0);
        prev = Number(r.prev_revenue || 0);
    } else if (exRegionalCategory === 'growth') {
        cur = Number(r.growth || 0);
        prev = Number(r.prev_growth || 0);
    } else if (exRegionalCategory === 'cm') {
        cur = Number(r.cm || 0);
        prev = Number(r.prev_cm || 0);
    } else if (exRegionalCategory === 'volume') {
        cur = Number(r.volume || 0);
        prev = Number(r.prev_volume || 0);
    }
    
    var change = exCalcRegionalChange(cur, prev);
    
    return { current: cur, previous: prev, change: change };
}

// Render the region stock indicator list
function exRenderRegionStockList() {
    var listEl = document.getElementById('exRegionStockList');
    if (!listEl) return;
    
    if (!exRegionalData.length) {
        listEl.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#94a3b8;font-size:0.8rem;padding:1rem;">No regional data.</div>';
        return;
    }
    
    // Define region display order (fixed)
    var regionOrder = ['SOUTH LUZON', 'NORTH & CENTRAL LUZON', 'VISAYAS', 'MINDANAO'];
    
    // Sort data based on current category (value descending)
    var sortedRegions = exRegionalData.slice().sort(function(a, b) {
        var va = exGetRegionMetric(a).current;
        var vb = exGetRegionMetric(b).current;
        return vb - va;
    });
    
    // Category color scheme
    var catColors = {
        revenue: { accent: '#0B7A3B', bg: '#f0fdf4', border: '#bbf7d0' },
        growth:   { accent: '#E31C23', bg: '#fef2f2', border: '#fecaca' },
        cm:       { accent: '#F59E0B', bg: '#fffbeb', border: '#fde68a' },
        volume:   { accent: '#2D6CDF', bg: '#eff6ff', border: '#bfdbfe' }
    };
    var cc = catColors[exRegionalCategory] || catColors.revenue;
    
    var html = '';
    
    sortedRegions.forEach(function(r, i) {
        var metric = exGetRegionMetric(r);
        var isUp = metric.change >= 0;
        var arrow = isUp ? '▲' : '▼';
        var arrowColor = isUp ? '#059669' : '#dc2626';
        var changeText = (isUp ? '+' : '') + Number(metric.change).toFixed(1) + '%';
        
        // Determine rank badge color
        var rankColors = ['#0B7A3B', '#2D6CDF', '#F59E0B', '#6B7280'];
        var rankColor = rankColors[i % rankColors.length];
        
        html += '<div style="display:flex;align-items:center;gap:0.5rem;padding:0.55rem 0.65rem;background:' + cc.bg + ';border:1px solid ' + cc.border + ';border-left:4px solid ' + cc.accent + ';border-radius:var(--radius-md);transition:all 0.15s;cursor:default;">' +
            // Rank badge
            '<div style="width:22px;height:22px;border-radius:50%;background:' + rankColor + ';color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.6rem;font-weight:800;flex-shrink:0;">' + (i + 1) + '</div>' +
            // Region label - one line, with ellipsis (...) for long names
            '<div style="flex:1;min-width:0;">' +
                '<div style="font-size:0.6rem;font-weight:800;color:#1e293b;text-transform:uppercase;letter-spacing:0.3px;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="' + (r.region || 'N/A') + '">' + (r.region || 'N/A') + '</div>' +
                '<div style="font-size:0.55rem;font-weight:500;color:' + cc.accent + ';margin-top:1px;">' + exRegionalCategory.toUpperCase() + '</div>' +
            '</div>' +
            // Value
            '<div style="text-align:right;flex-shrink:0;">' +
                '<div style="font-size:0.78rem;font-weight:800;color:#1e293b;letter-spacing:-0.3px;line-height:1.1;">' + exFormatRegionalValue(metric.current) + '</div>' +
                '<div style="display:flex;align-items:center;justify-content:flex-end;gap:2px;font-size:0.6rem;font-weight:800;color:' + arrowColor + ';margin-top:2px;">' +
                    '<span style="font-size:0.55rem;">' + arrow + '</span>' +
                    '<span>' + changeText + '</span>' +
                '</div>' +
            '</div>' +
        '</div>';
    });
    
    listEl.innerHTML = html;
}

// ===== TOP PERFORMERS =====
function renderTopPerformers(data) {
    var top10 = data.top10 || [];
    var body = document.getElementById('exTopPerformersBody');
    
    if (!top10.length) {
        body.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:1.5rem;color:#94a3b8;">No performer data available.</td></tr>';
        return;
    }
    
    var html = '';
    top10.forEach(function(p, i) {
        var rankColor = i === 0 ? '#FFD700' : (i === 1 ? '#C0C0C0' : (i === 2 ? '#CD7F32' : '#94a3b8'));
        var initials = (p.name || '?').charAt(0).toUpperCase();
        var bgColors = ['#0B7A3B', '#2D6CDF', '#E31C23', '#7A4DFF', '#F59E0B', '#00897B', '#6B7280', '#D97706', '#7C3AED', '#059669'];
        
        html += '<tr>' +
            '<td class="rank-cell"><div class="rank-badge" style="background:' + rankColor + ';color:' + (i < 3 ? '#1a1a2e' : '#fff') + ';">' + (i + 1) + '</div></td>' +
            '<td><div style="display:flex;align-items:center;gap:0.4rem;"><div class="avatar-sm" style="background:' + bgColors[i % bgColors.length] + ';">' + initials + '</div><span class="name-cell">' + (p.name || 'N/A') + '</span></div></td>' +
            '<td class="dboard-cell">' + (p.dashboard || '—') + '</td>' +
            '<td class="attainment-cell">' + (p.metric || '—') + '</td>' +
            '</tr>';
    });
    body.innerHTML = html;
}

// ===== DASHBOARD & CATEGORY FILTER LOGIC =====
// Category options per dashboard
var exCategoryMap = {
    'se': [
        { value: 'attainment', label: 'HIGHEST % ATTAINMENT OVER BUDGET' },
        { value: 'margin', label: 'HIGHEST % CONTRIBUTION MARGIN' }
    ],
    'tb': [
        { value: 'margin', label: 'HIGHEST % GROWTH VS LM' },
        { value: 'attainment', label: 'HIGHEST % OVERALL ATTAINMENT VS BUDGET' }
    ],
    'ec': [
        { value: 'volume', label: 'HIGHEST VOLUME CONTRIBUTOR PER REGION' },
        { value: 'margin', label: 'HIGHEST CONTRIBUTION MARGIN PER REGION' }
    ]
};

// Update category dropdown based on selected dashboard
function exUpdateCategoryOptions() {
    var dashEl = document.getElementById('exDashboardFilter');
    var catEl = document.getElementById('exCategoryFilter');
    var nameEl = document.getElementById('exNameFilter');
    var dash = dashEl.value;
    
    // Clear current options
    catEl.innerHTML = '<option value="">-- Select Category --</option>';
    // Reset name dropdown
    nameEl.innerHTML = '<option value="">-- Select Name --</option>';
    nameEl.disabled = true;
    
    if (dash && exCategoryMap[dash]) {
        exCategoryMap[dash].forEach(function(cat) {
            var opt = document.createElement('option');
            opt.value = cat.value;
            opt.textContent = cat.label;
            catEl.appendChild(opt);
        });
    }
    
    // Reset search results when filters change
    exResetAll();
}

// Load participant names for the selected dashboard + category
function exLoadParticipantNames() {
    var dashEl = document.getElementById('exDashboardFilter');
    var catEl = document.getElementById('exCategoryFilter');
    var nameEl = document.getElementById('exNameFilter');
    var d = dashEl.value;
    var c = catEl.value;
    
    // Reset name dropdown
    nameEl.innerHTML = '<option value="">-- Select Name --</option>';
    nameEl.disabled = true;
    
    // Reset results
    exResetAll();
    
    if (!d || !c) return;
    
    var m = document.getElementById('exMonth') ? document.getElementById('exMonth').value : new Date().getMonth() + 1;
    var y = document.getElementById('exYear') ? document.getElementById('exYear').value : new Date().getFullYear();
    
    // Show loading in name dropdown
    nameEl.innerHTML = '<option value="">Loading names...</option>';
    
    window.fetch('/executive/api/participant-names?month=' + m + '&year=' + y + '&dashboard=' + d + '&category=' + c)
        .then(function(r) { return r.json(); })
        .then(function(j) {
            if (j.success && j.data && j.data.length > 0) {
                nameEl.innerHTML = '<option value="">-- Select Name --</option>';
                j.data.forEach(function(p) {
                    var opt = document.createElement('option');
                    opt.value = p.name;
                    var regionText = p.region ? ' (' + p.region + ')' : '';
                    var officeText = (d === 'tb' && p.sales_office) ? ' — ' + p.sales_office : '';
                    opt.textContent = p.name + officeText + regionText;
                    nameEl.appendChild(opt);
                });
                nameEl.disabled = false;
            } else {
                nameEl.innerHTML = '<option value="">-- No names found --</option>';
                nameEl.disabled = true;
            }
        })
        .catch(function(e) {
            console.error('Load names error:', e);
            nameEl.innerHTML = '<option value="">-- Error loading names --</option>';
            nameEl.disabled = true;
        });
}

// Search participant when name is selected from dropdown
function exSearchSelectedName() {
    var nameEl = document.getElementById('exNameFilter');
    var q = nameEl.value;
    if (!q) { exResetAll(); return; }
    
    var d = document.getElementById('exDashboardFilter') ? document.getElementById('exDashboardFilter').value : '';
    var c = document.getElementById('exCategoryFilter') ? document.getElementById('exCategoryFilter').value : '';
    
    if (!d || !c) {
        alert('Please select a Dashboard and Category first.');
        return;
    }
    
    var m = document.getElementById('exMonth') ? document.getElementById('exMonth').value : new Date().getMonth() + 1;
    var y = document.getElementById('exYear') ? document.getElementById('exYear').value : new Date().getFullYear();
    
    // Show loading state on photo
    document.getElementById('exPhotoPlaceholder').innerHTML = '<div class="spinner" style="width:30px;height:30px;border:3px solid rgba(255,255,255,0.2);border-top-color:#fff;"></div>';
    
    window.fetch('/executive/api/participant-profile?name=' + encodeURIComponent(q) + '&month=' + m + '&year=' + y + '&dashboard=' + d + '&category=' + c)
        .then(function(r) { return r.json(); })
        .then(function(j) {
            if (j.success && j.data && j.data.dashboards && j.data.dashboards.length > 0) {
                renderParticipantData(j.data);
            } else {
                alert('No match found for "' + q + '" in the selected dashboard and category for the selected period.');
                exResetAll();
            }
        })
        .catch(function(e) { console.error(e); alert('Search error'); });
}

// ===== AUTO-FIT NAME FONT SIZE (prevents breaking long names) =====
function fitPhotoName() {
    var nameEl = document.getElementById('exPhotoName');
    if (!nameEl) return;
    
    var photoPlaceholder = document.getElementById('exPhotoPlaceholder');
    // Use the photo placeholder (red grid) width so NAME never exceeds it
    var maxWidth = photoPlaceholder ? photoPlaceholder.clientWidth : 270;
    // Ensure maxWidth never exceeds 270px (the red grid width)
    if (maxWidth > 270) maxWidth = 270;
    // Add small safety padding so name never visually touches grid edge
    maxWidth = Math.max(60, maxWidth - 8);
    
    // Clear any saved font size for name from localStorage
    try {
        var saved = JSON.parse(localStorage.getItem('ex_canva_state') || '{}');
        if (saved.exPhotoName) {
            delete saved.exPhotoName.fontSize;
            localStorage.setItem('ex_canva_state', JSON.stringify(saved));
        }
    } catch(e) {}
    
    // Force name width to match the actual placeholder width - never wider
    nameEl.style.width = maxWidth + 'px';
    nameEl.style.maxWidth = maxWidth + 'px';
    
    // Force name position to CSS default (centered) - never allow it to shift down
    nameEl.style.position = 'absolute';
    nameEl.style.top = '19.5rem';
    nameEl.style.left = '50%';
    nameEl.style.right = 'auto';
    nameEl.style.bottom = 'auto';
    nameEl.style.transform = 'translateX(-50%)';
    nameEl.style.margin = '0';
    nameEl.style.padding = '0';
    nameEl.style.whiteSpace = 'nowrap';
    nameEl.style.overflow = 'hidden';
    nameEl.style.textOverflow = 'ellipsis';
    nameEl.style.display = 'block';
    
    // Reset to base size first (large by default)
    nameEl.style.fontSize = '2.8rem';
    
    // Use a temporary span to measure text width accurately
    var text = nameEl.textContent || '';
    if (!text || text === '—') return;
    
    var temp = document.createElement('span');
    temp.style.cssText = 'position:absolute;visibility:hidden;white-space:nowrap;font-weight:900;text-transform:uppercase;font-family:inherit;';
    temp.textContent = text;
    document.body.appendChild(temp);
    
    // Measure and shrink until it fits on one line
    var currentSize = 2.8;
    var minSize = 0.7;
    temp.style.fontSize = currentSize + 'rem';
    
    while (temp.offsetWidth > maxWidth && currentSize > minSize) {
        currentSize -= 0.05;
        temp.style.fontSize = currentSize + 'rem';
    }
    
    document.body.removeChild(temp);
    nameEl.style.fontSize = currentSize + 'rem';
}

// ===== RENDER PARTICIPANT DATA =====
function renderParticipantData(data) {
    // Hide empty state, show detail table + AI section only
    document.getElementById('exEmptyState').style.display = 'none';
    document.getElementById('exDetailTableWrap').style.display = 'block';
    document.getElementById('exAiSection').classList.add('show');
    
    // ===== 1. PHOTO SQUARE with Corporate Design =====
    var photoSquare = document.getElementById('exPhotoSquare');
    var photoEl = document.getElementById('exPhotoPlaceholder');
    var nameEl = document.getElementById('exPhotoName');
    var posEl = document.getElementById('exPhotoPosition');
    
    nameEl.textContent = data.name || '—';
    // Format: POSITION - AREA
    var positionText = (data.position || '').toUpperCase();
    var areaText = (data.area || data.region || '').toUpperCase();
    posEl.textContent = positionText + ' - ' + areaText;
    
    // Photo - check for photo URL
    if (data.photo) {
        var photoUrl = data.photo;
        photoEl.innerHTML = '<img src="' + photoUrl + '" alt="' + (data.name || 'Photo') + '" style="width:100%;height:100%;object-fit:contain;border-radius:12px;display:block;max-width:100%;max-height:100%;" onerror="console.error(\'Photo load failed:\', this.src);this.parentElement.innerHTML=\'<span style=\\\'font-size:3rem;color:rgba(255,255,255,0.6);\\\'>' + (data.name ? data.name.charAt(0).toUpperCase() : '?') + '</span>\';this.parentElement.classList.remove(\'has-photo\');">';
        photoEl.classList.add('has-photo');
    } else {
        photoEl.innerHTML = '<span style="font-size:3rem;color:rgba(255,255,255,0.6);">' + (data.name ? data.name.charAt(0).toUpperCase() : '?') + '</span>';
        photoEl.classList.remove('has-photo');
    }
    
    // ===== RANK-BASED BACKGROUND IMAGE (1st.png / 2nd.png / 3rd.png) =====
    // Reset photo square classes (keep base only)
    photoSquare.className = 'ex-photo-square';
    
    // Parse rank from rank_status like "1st Place", "2nd Place", "3rd Place", or "GRAND SLAM"
    var rankStatus = data.rank_status || '';
    
    // Get the numeric rank from the rank status string
    var rankMatch = rankStatus.match(/(\d+)\s*(?:st|nd|rd|th)/i);
    if (rankMatch) {
        var rankNum = parseInt(rankMatch[1]);
        if (rankNum === 1) {
            photoSquare.classList.add('rank-1st');
        } else if (rankNum === 2) {
            photoSquare.classList.add('rank-2nd');
        } else if (rankNum === 3) {
            photoSquare.classList.add('rank-3rd');
        }
    } else if (rankStatus.toUpperCase().indexOf('GRAND SLAM') !== -1) {
        photoSquare.classList.add('rank-grand-slam');
    }
    
    // Determine best category from dashboards
    var dashboards = data.dashboards || [];
    var bestCategory = '';
    var bestAttainment = 0;
    dashboards.forEach(function(d) {
        var att = parseFloat(d.attainment) || 0;
        if (att > bestAttainment) {
            bestAttainment = att;
            bestCategory = d.category || '';
        }
    });
    
    // Show category below position
    var catEl = document.getElementById('exPhotoCategory');
    if (bestCategory) {
        catEl.textContent = bestCategory.toUpperCase();
        catEl.style.display = 'block';
    } else {
        catEl.textContent = '—';
        catEl.style.display = 'block';
    }
    
    // ===== 2. DETAIL TABLE - DYNAMIC COLUMNS BASED ON DASHBOARD + CATEGORY =====
    var dash = document.getElementById('exDashboardFilter').value;
    var cat = document.getElementById('exCategoryFilter').value;
    var tbody = document.getElementById('exDetailBody');
    var thead = document.getElementById('exDetailHead');
    
    // Define column headers based on dashboard + category
    var columns = [];
    if (dash === 'se' && cat === 'attainment') {
        columns = ['% ATTAINMENT', 'ACTUAL', 'BUDGET'];
    } else if (dash === 'se' && cat === 'margin') {
        columns = ['REVENUE', 'ACTUAL', 'PRICE/LF'];
    } else if (dash === 'tb' && cat === 'margin') {
        columns = ['LAST MONTH', 'CURRENT MONTH', '% GROWTH'];
    } else if (dash === 'tb' && cat === 'attainment') {
        columns = ['% ATTAINMENT', 'ACTUAL', 'BUDGET'];
    } else if (dash === 'ec' && cat === 'volume') {
        columns = ['ACTUAL VOLUME'];
    } else if (dash === 'ec' && cat === 'margin') {
        columns = ['ACTUAL CM'];
    } else {
        columns = ['VALUE'];
    }
    
    // Build table header
    var headHtml = '<tr><th>DASHBOARD</th><th>CATEGORY</th>';
    columns.forEach(function(col) {
        headHtml += '<th>' + col + '</th>';
    });
    headHtml += '<th>STATUS</th></tr>';
    thead.innerHTML = headHtml;
    
    document.getElementById('exDetailCount').textContent = dashboards.length + ' Record' + (dashboards.length !== 1 ? 's' : '');
    
    if (dashboards.length === 0) {
        tbody.innerHTML = '<tr><td colspan="' + (columns.length + 3) + '" style="padding:1.5rem;text-align:center;color:#94a3b8;">No dashboard records found.</td></tr>';
    } else {
        var html = '';
        dashboards.forEach(function(d) {
            var statusClass = (d.status === 'Active') ? 'status-active' : 'status-pending';
            
            // Build data cells based on dashboard + category
            var dataCells = '';
            if (dash === 'se' && cat === 'attainment') {
                dataCells = 
                    '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;font-weight:700;color:#059669;">' + (d.attainment ? Number(d.attainment).toFixed(2) + '%' : '—') + '</td>' +
                    '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;">' + (d.actual_volume ? fmtNum(d.actual_volume) : '—') + '</td>' +
                    '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;">' + (d.budget ? fmtNum(d.budget) : '—') + '</td>';
            } else if (dash === 'se' && cat === 'margin') {
                dataCells = 
                    '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;font-weight:700;color:#059669;">' + (d.revenue ? fmtPeso(d.revenue) : '—') + '</td>' +
                    '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;">' + (d.actual_cm ? fmtPeso(d.actual_cm) : '—') + '</td>' +
                    '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;">' + (d.price_lf ? Number(d.price_lf).toFixed(2) : '—') + '</td>';
            } else if (dash === 'tb' && cat === 'margin') {
                dataCells = 
                    '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;">' + (d.last_month ? fmtNum(d.last_month) : '—') + '</td>' +
                    '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;">' + (d.current_month ? fmtNum(d.current_month) : '—') + '</td>' +
                    '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;font-weight:700;color:#059669;">' + (d.growth ? Number(d.growth).toFixed(2) + '%' : '—') + '</td>';
            } else if (dash === 'tb' && cat === 'attainment') {
                dataCells = 
                    '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;font-weight:700;color:#059669;">' + (d.attainment ? Number(d.attainment).toFixed(2) + '%' : '—') + '</td>' +
                    '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;">' + (d.actual ? fmtNum(d.actual) : '—') + '</td>' +
                    '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;">' + (d.budget ? fmtNum(d.budget) : '—') + '</td>';
            } else if (dash === 'ec' && cat === 'volume') {
                dataCells = 
                    '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;font-weight:700;color:#059669;">' + (d.volume ? fmtNum(d.volume) : '—') + '</td>';
            } else if (dash === 'ec' && cat === 'margin') {
                dataCells = 
                    '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;font-weight:700;color:#059669;">' + (d.gross_amount ? fmtPeso(d.gross_amount) : '—') + '</td>';
            } else {
                dataCells = '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;">—</td>';
            }
            
            html += '<tr>' +
                '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;font-weight:700;">' + (d.dashboard || '—') + '</td>' +
                '<td style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;color:#334155;vertical-align:middle;">' + (d.category || '—').toUpperCase() + '</td>' +
                dataCells +
                '<td class="' + statusClass + '" style="padding:0.5rem 0.4rem;border:1px solid #e2e8f0;text-align:center;font-size:0.65rem;vertical-align:middle;font-weight:700;">' + (d.status || 'Active') + '</td>' +
                '</tr>';
        });
        tbody.innerHTML = html;
    }
    
    // ===== 3. SMART ANALYTICS AI GENERATION =====
    generateSmartAnalytics(data);
    
    // ===== 4. RE-APPLY CANVA LAYOUT (position/size/styles) =====
    // Reset name position to CSS default FIRST so it always stays centered (top:20rem, left:50%)
    // This prevents any previously saved canva drag position from pushing the name down
    nameEl.style.left = '';
    nameEl.style.top = '';
    nameEl.style.transform = '';
    restoreCanvaState();
    
    // ===== 5. AUTO-FIT NAME FONT SIZE (only shrinks if name is too long) =====
    fitPhotoName();
    
    // ===== 6. MAKE PHOTO SQUARE CLICKABLE TO EXPAND =====
    photoSquare.classList.add('clickable');
    photoSquare.title = 'Click to expand placement';
    
    // Add click handler to open the expand modal
    photoSquare.onclick = function(e) {
        // Don't open if in canva mode
        if (photoSquare.classList.contains('canva-mode')) return;
        exOpenPhotoModal();
    };
}

// Close photo modal when clicking outside it
document.addEventListener('DOMContentLoaded', function() {
    var photoOverlay = document.getElementById('exPhotoModalOverlay');
    if (photoOverlay) {
        photoOverlay.addEventListener('click', function(e) {
            if (e.target === photoOverlay) {
                exClosePhotoModal();
            }
        });
    }
});

// ===== SMART ANALYTICS AI - AUTO GENERATED FINDINGS & RECOMMENDATIONS =====
// This AI analyzes the EMPLOYEE DATA table (filtered by selected dashboard + category)
// to generate data summary and insights based on the actual values shown in the table.
function generateSmartAnalytics(data) {
    var dashboards = data.dashboards || [];
    var totalDashboards = dashboards.length;
    
    // Get the currently selected dashboard + category (same as the EMPLOYEE DATA table)
    var dash = document.getElementById('exDashboardFilter') ? document.getElementById('exDashboardFilter').value : '';
    var cat = document.getElementById('exCategoryFilter') ? document.getElementById('exCategoryFilter').value : '';
    
    // ===== ANALYZE THE EMPLOYEE DATA TABLE RECORDS (filtered by dashboard + category) =====
    // Find the specific dashboard record(s) that match the selected dashboard + category
    var tableRecords = dashboards.filter(function(d) {
        var dCat = (d.category || '').toLowerCase();
        var dName = (d.dashboard || '').toUpperCase();
        
        // Match by dashboard type
        var matchesDash = false;
        if (dash === 'se' && dName.indexOf('SALES EXCELLENCE') !== -1) matchesDash = true;
        else if (dash === 'tb' && dName.indexOf('TOP BRANCH') !== -1) matchesDash = true;
        else if (dash === 'ec' && dName.indexOf('ELITE CIRCLE') !== -1) matchesDash = true;
        else if (dash === '') matchesDash = true; // No dashboard filter
        
        // Match by category
        var matchesCat = false;
        if (cat === 'all' || cat === '') matchesCat = true;
        else if (dCat === cat) matchesCat = true;
        
        return matchesDash && matchesCat;
    });
    
    // If no exact match, fall back to all dashboards
    if (tableRecords.length === 0) tableRecords = dashboards;
    
    // ===== EXTRACT DATA FROM THE EMPLOYEE DATA TABLE RECORDS =====
    var totalRevenue = 0;
    var totalVolume = 0;
    var totalCm = 0;
    var maxAttainment = 0;
    var totalAttainment = 0;
    var attainmentCount = 0;
    var totalGrowth = 0;
    var growthCount = 0;
    var totalActual = 0;
    var totalBudget = 0;
    var totalLastMonth = 0;
    var totalCurrentMonth = 0;
    var totalPriceLf = 0;
    var priceLfCount = 0;
    
    tableRecords.forEach(function(d) {
        // Revenue (from SE revenue or EC gross_amount)
        var rev = parseFloat(d.revenue) || 0;
        var gross = parseFloat(d.gross_amount) || 0;
        totalRevenue += rev + gross;
        
        // Volume (from EC volume or SE actual_volume)
        totalVolume += parseFloat(d.volume) || 0;
        totalVolume += parseFloat(d.actual_volume) || 0;
        
        // CM (from EC gross_amount or SE actual_cm)
        totalCm += parseFloat(d.actual_cm) || 0;
        totalCm += parseFloat(d.gross_amount) || 0;
        
        // Attainment
        if (d.attainment) {
            var att = parseFloat(d.attainment);
            maxAttainment = Math.max(maxAttainment, att);
            totalAttainment += att;
            attainmentCount++;
        }
        
        // Growth
        if (d.growth) {
            totalGrowth += parseFloat(d.growth);
            growthCount++;
        }
        
        // Actual / Budget
        totalActual += parseFloat(d.actual) || 0;
        totalActual += parseFloat(d.actual_volume) || 0;
        totalBudget += parseFloat(d.budget) || 0;
        
        // Last Month / Current Month
        totalLastMonth += parseFloat(d.last_month) || 0;
        totalCurrentMonth += parseFloat(d.current_month) || 0;
        
        // Price/LF
        if (d.price_lf) {
            totalPriceLf += parseFloat(d.price_lf);
            priceLfCount++;
        }
    });
    
    var avgAttainment = attainmentCount > 0 ? (totalAttainment / attainmentCount) : 0;
    var avgGrowth = growthCount > 0 ? (totalGrowth / growthCount) : 0;
    var avgPriceLf = priceLfCount > 0 ? (totalPriceLf / priceLfCount) : 0;
    
    // ===== AI METRICS - Based on the EMPLOYEE DATA TABLE =====
    var metricsEl = document.getElementById('exAiMetrics');
    var metricsHtml = '';
    
    // Dashboard Coverage
    metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Dashboard Coverage</div><div class="ai-m-value blue">' + totalDashboards + ' / 3</div></div>';
    
    // Show metrics based on the selected dashboard + category (matching the table columns)
    if (dash === 'se' && cat === 'attainment') {
        // Table shows: % ATTAINMENT, ACTUAL, BUDGET
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">% Attainment</div><div class="ai-m-value gold">' + (maxAttainment ? Number(maxAttainment).toFixed(2) + '%' : '—') + '</div></div>';
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Actual</div><div class="ai-m-value green">' + fmtNum(totalActual) + '</div></div>';
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Budget</div><div class="ai-m-value">' + fmtNum(totalBudget) + '</div></div>';
    } else if (dash === 'se' && cat === 'margin') {
        // Table shows: REVENUE, ACTUAL, PRICE/LF
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Revenue</div><div class="ai-m-value green">' + fmtPeso(totalRevenue) + '</div></div>';
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Actual CM</div><div class="ai-m-value">' + fmtPeso(totalCm) + '</div></div>';
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Price/LF</div><div class="ai-m-value">' + (avgPriceLf ? Number(avgPriceLf).toFixed(2) : '—') + '</div></div>';
    } else if (dash === 'tb' && cat === 'margin') {
        // Table shows: LAST MONTH, CURRENT MONTH, % GROWTH
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Last Month</div><div class="ai-m-value">' + fmtNum(totalLastMonth) + '</div></div>';
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Current Month</div><div class="ai-m-value green">' + fmtNum(totalCurrentMonth) + '</div></div>';
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">% Growth</div><div class="ai-m-value gold">' + (avgGrowth ? Number(avgGrowth).toFixed(2) + '%' : '—') + '</div></div>';
    } else if (dash === 'tb' && cat === 'attainment') {
        // Table shows: % ATTAINMENT, ACTUAL, BUDGET
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">% Attainment</div><div class="ai-m-value gold">' + (maxAttainment ? Number(maxAttainment).toFixed(2) + '%' : '—') + '</div></div>';
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Actual</div><div class="ai-m-value green">' + fmtNum(totalActual) + '</div></div>';
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Budget</div><div class="ai-m-value">' + fmtNum(totalBudget) + '</div></div>';
    } else if (dash === 'ec' && cat === 'volume') {
        // Table shows: ACTUAL VOLUME
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Total Volume</div><div class="ai-m-value green">' + fmtNum(totalVolume) + '</div></div>';
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Revenue</div><div class="ai-m-value">' + fmtPeso(totalRevenue) + '</div></div>';
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Rank Status</div><div class="ai-m-value">' + (data.rank_status || 'NO RECORD') + '</div></div>';
    } else if (dash === 'ec' && cat === 'margin') {
        // Table shows: ACTUAL CM
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Total CM</div><div class="ai-m-value green">' + fmtPeso(totalCm) + '</div></div>';
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Revenue</div><div class="ai-m-value">' + fmtPeso(totalRevenue) + '</div></div>';
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Rank Status</div><div class="ai-m-value">' + (data.rank_status || 'NO RECORD') + '</div></div>';
    } else {
        // Default: show general metrics
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Total Revenue</div><div class="ai-m-value green">' + fmtPeso(totalRevenue) + '</div></div>';
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Avg Attainment</div><div class="ai-m-value gold">' + (avgAttainment ? Number(avgAttainment).toFixed(2) + '%' : '—') + '</div></div>';
        metricsHtml += '<div class="ex-ai-metric"><div class="ai-m-label">Rank Status</div><div class="ai-m-value">' + (data.rank_status || 'NO RECORD') + '</div></div>';
    }
    
    metricsEl.innerHTML = metricsHtml;
    
    // ===== AI DATA SUMMARY - Based on the EMPLOYEE DATA TABLE =====
    var findingsEl = document.getElementById('exAiFindingsText');
    var findings = '';
    
    findings += '<strong>' + (data.name || 'This participant') + '</strong> — ';
    
    if (tableRecords.length === 0) {
        findings += 'no matching record found in the EMPLOYEE DATA table for the selected dashboard and category. No performance data is available for analysis.';
    } else {
        // Build data summary based on the selected dashboard + category
        var dashLabel = '';
        if (dash === 'se') dashLabel = 'SALES EXCELLENCE AWARDEE';
        else if (dash === 'tb') dashLabel = 'TOP BRANCH RECOGNITION';
        else if (dash === 'ec') dashLabel = 'ELITE CIRCLE';
        else dashLabel = 'SELECTED DASHBOARD';
        
        var catLabel = (cat || '').toUpperCase();
        
        findings += 'based on the <strong>EMPLOYEE DATA</strong> for <strong>' + dashLabel + '</strong>';
        if (catLabel) findings += ' — <strong>' + catLabel + '</strong>';
        findings += ', the following data summary is generated:<br><br>';
        
        // Data summary based on dashboard + category
        if (dash === 'se' && cat === 'attainment') {
            findings += '📊 <strong>Attainment:</strong> ' + (maxAttainment ? Number(maxAttainment).toFixed(2) + '%' : '—') + ' (highest), with an average of <strong>' + Number(avgAttainment).toFixed(2) + '%</strong> across ' + attainmentCount + ' record(s).<br>';
            findings += '📦 <strong>Actual Volume:</strong> ' + fmtNum(totalActual) + ' vs <strong>Budget:</strong> ' + fmtNum(totalBudget) + '.<br>';
            if (totalBudget > 0) {
                var actualVsBudget = (totalActual / totalBudget) * 100;
                findings += '⚖️ <strong>Actual vs Budget:</strong> ' + Number(actualVsBudget).toFixed(1) + '% of the budget was achieved.';
            }
        } else if (dash === 'se' && cat === 'margin') {
            findings += '💰 <strong>Revenue:</strong> ' + fmtPeso(totalRevenue) + '.<br>';
            findings += '📊 <strong>Contribution Margin:</strong> ' + fmtPeso(totalCm) + '.<br>';
            findings += '🏷️ <strong>Price/LF:</strong> ' + (avgPriceLf ? '₱' + Number(avgPriceLf).toFixed(2) : '—') + ' average.';
        } else if (dash === 'tb' && cat === 'margin') {
            findings += '📈 <strong>Last Month:</strong> ' + fmtNum(totalLastMonth) + ' → <strong>Current Month:</strong> ' + fmtNum(totalCurrentMonth) + '.<br>';
            findings += '📊 <strong>Growth:</strong> ' + (avgGrowth ? Number(avgGrowth).toFixed(2) + '%' : '—') + ' average growth vs last month.<br>';
            if (totalLastMonth > 0) {
                var growthCalc = ((totalCurrentMonth - totalLastMonth) / totalLastMonth) * 100;
                findings += '⚖️ <strong>Month-over-Month Change:</strong> ' + Number(growthCalc).toFixed(1) + '%.';
            }
        } else if (dash === 'tb' && cat === 'attainment') {
            findings += '📊 <strong>Attainment:</strong> ' + (maxAttainment ? Number(maxAttainment).toFixed(2) + '%' : '—') + ' (highest), with an average of <strong>' + Number(avgAttainment).toFixed(2) + '%</strong> across ' + attainmentCount + ' record(s).<br>';
            findings += '📦 <strong>Actual:</strong> ' + fmtNum(totalActual) + ' vs <strong>Budget:</strong> ' + fmtNum(totalBudget) + '.<br>';
            if (totalBudget > 0) {
                var actualVsBudgetTb = (totalActual / totalBudget) * 100;
                findings += '⚖️ <strong>Actual vs Budget:</strong> ' + Number(actualVsBudgetTb).toFixed(1) + '% of the budget was achieved.';
            }
        } else if (dash === 'ec' && cat === 'volume') {
            findings += '📦 <strong>Total Volume:</strong> ' + fmtNum(totalVolume) + '.<br>';
            findings += '💰 <strong>Revenue:</strong> ' + fmtPeso(totalRevenue) + '.<br>';
            findings += '📊 <strong>Volume Performance:</strong> ' + (totalVolume > 0 ? 'Active volume contributor in the Elite Circle program.' : 'No volume data recorded for this period.');
        } else if (dash === 'ec' && cat === 'margin') {
            findings += '💰 <strong>Total Contribution Margin:</strong> ' + fmtPeso(totalCm) + '.<br>';
            findings += '📊 <strong>Revenue:</strong> ' + fmtPeso(totalRevenue) + '.<br>';
            findings += '📈 <strong>Margin Performance:</strong> ' + (totalCm > 0 ? 'Strong margin contribution in the Elite Circle program.' : 'No margin data recorded for this period.');
        } else {
            // General summary
            findings += '📊 <strong>Total Revenue:</strong> ' + fmtPeso(totalRevenue) + '.<br>';
            findings += '📦 <strong>Total Volume:</strong> ' + fmtNum(totalVolume) + '.<br>';
            findings += '🎯 <strong>Avg Attainment:</strong> ' + (avgAttainment ? Number(avgAttainment).toFixed(2) + '%' : '—') + '.<br>';
            findings += '📈 <strong>Avg Growth:</strong> ' + (avgGrowth ? Number(avgGrowth).toFixed(2) + '%' : '—') + '.';
        }
        
        // ===== AI INSIGHTS - Based on the actual data values =====
        findings += '<br><br><strong>🔍 Key Insights:</strong><br>';
        
        // Attainment insight
        if (attainmentCount > 0) {
            if (avgAttainment >= 100) {
                findings += '🎯 <strong>Outstanding Attainment:</strong> Exceeding 100% target (' + Number(avgAttainment).toFixed(2) + '%) indicates exceptional performance above expectations.';
            } else if (avgAttainment >= 80) {
                findings += '📈 <strong>Strong Attainment:</strong> At ' + Number(avgAttainment).toFixed(2) + '%, performance is above the 80% threshold, showing reliable and consistent execution.';
            } else if (avgAttainment >= 50) {
                findings += '📊 <strong>Developing Attainment:</strong> At ' + Number(avgAttainment).toFixed(2) + '%, there is room for improvement. Focused coaching may help boost performance.';
            } else if (avgAttainment > 0) {
                findings += '⚠️ <strong>Needs Attention:</strong> At ' + Number(avgAttainment).toFixed(2) + '%, significant improvement is needed. A performance improvement plan is recommended.';
            }
        }
        
        // Growth insight
        if (growthCount > 0) {
            if (avgGrowth > 0) {
                findings += '<br>📈 <strong>Positive Growth:</strong> ' + Number(avgGrowth).toFixed(2) + '% average growth vs last month indicates upward momentum.';
            } else if (avgGrowth < 0) {
                findings += '<br>📉 <strong>Negative Growth:</strong> ' + Number(avgGrowth).toFixed(2) + '% average growth shows a decline vs last month. Immediate attention needed.';
            } else {
                findings += '<br>📊 <strong>Flat Growth:</strong> No change vs last month. Consider new strategies to drive growth.';
            }
        }
        
        // Budget vs Actual insight
        if (totalBudget > 0 && totalActual > 0) {
            var budgetRatio = (totalActual / totalBudget) * 100;
            if (budgetRatio >= 100) {
                findings += '<br>✅ <strong>Budget Exceeded:</strong> Actual (' + fmtNum(totalActual) + ') exceeded budget (' + fmtNum(totalBudget) + ') by ' + Number(budgetRatio - 100).toFixed(1) + '%.';
            } else if (budgetRatio >= 80) {
                findings += '<br>📊 <strong>Near Budget:</strong> Actual (' + fmtNum(totalActual) + ') is at ' + Number(budgetRatio).toFixed(1) + '% of budget (' + fmtNum(totalBudget) + ').';
            } else {
                findings += '<br>⚠️ <strong>Below Budget:</strong> Actual (' + fmtNum(totalActual) + ') is only ' + Number(budgetRatio).toFixed(1) + '% of budget (' + fmtNum(totalBudget) + ').';
            }
        }
        
        // Volume insight
        if (totalVolume > 0) {
            findings += '<br>📦 <strong>Volume Contribution:</strong> Total volume of ' + fmtNum(totalVolume) + ' demonstrates active market presence.';
        }
        
        // Revenue insight
        if (totalRevenue > 0) {
            findings += '<br>💰 <strong>Revenue Impact:</strong> ' + fmtPeso(totalRevenue) + ' in total revenue generated for the organization.';
        }
        
        // Rank insight
        if (data.rank_status && data.rank_status !== 'No Record' && data.rank_status !== '') {
            findings += '<br>🏆 <strong>Ranking:</strong> Currently ranked <strong>' + data.rank_status + '</strong> in the leaderboard.';
        }
    }
    
    findingsEl.innerHTML = findings;
    
    // ===== AI RECOMMENDATIONS - Based on the EMPLOYEE DATA TABLE =====
    var recEl = document.getElementById('exAiRecText');
    var recommendations = [];
    
    if (tableRecords.length === 0) {
        recommendations.push('No matching record found in the EMPLOYEE DATA table for the selected dashboard and category.');
        recommendations.push('Verify that the participant is enrolled in the selected dashboard program for the chosen period.');
        recommendations.push('Consider enrolling the participant in the selected dashboard program to begin performance tracking.');
    } else {
        // Attainment recommendations
        if (attainmentCount > 0 && avgAttainment < 100) {
            var gap = (100 - avgAttainment).toFixed(1);
            recommendations.push('Close the attainment gap of ' + gap + '% by focusing on high-margin products and strategic upselling techniques.');
        } else if (attainmentCount > 0 && avgAttainment >= 100) {
            recommendations.push('Maintain the exceptional attainment level (' + Number(avgAttainment).toFixed(2) + '%) by sustaining current strategies and exploring new growth opportunities.');
        }
        
        // Growth recommendations
        if (growthCount > 0 && avgGrowth < 0) {
            recommendations.push('Address the negative growth trend (' + Number(avgGrowth).toFixed(2) + '%) by reviewing market conditions and adjusting the sales approach.');
        } else if (growthCount > 0 && avgGrowth > 0) {
            recommendations.push('Capitalize on the positive growth momentum (' + Number(avgGrowth).toFixed(2) + '%) by expanding into adjacent markets or product lines.');
        }
        
        // Budget recommendations
        if (totalBudget > 0 && totalActual > 0 && totalActual < totalBudget) {
            var shortfall = totalBudget - totalActual;
            recommendations.push('Address the budget shortfall of ' + fmtNum(shortfall) + ' by identifying underperforming areas and implementing corrective actions.');
        }
        
        // Volume recommendations
        if (dash === 'ec' && cat === 'volume' && totalVolume > 0) {
            recommendations.push('Leverage the current volume (' + fmtNum(totalVolume) + ') to negotiate better terms and increase contribution margin.');
        }
        
        // CM recommendations
        if (dash === 'ec' && cat === 'margin' && totalCm > 0) {
            recommendations.push('Protect and grow the contribution margin (' + fmtPeso(totalCm) + ') by optimizing pricing and managing costs effectively.');
        }
        
        // Dashboard coverage recommendations
        if (totalDashboards < 3) {
            var remaining = 3 - totalDashboards;
            recommendations.push('Expand dashboard coverage by enrolling in ' + remaining + ' additional program(s) to pursue GRAND SLAM status and maximize total earnings.');
        } else {
            recommendations.push('Maintain GRAND SLAM status by ensuring consistent participation and performance across all 3 dashboard programs.');
        }
        
        // General recommendations
        recommendations.push('Schedule a quarterly performance review to track progress against targets and adjust strategies as needed.');
        recommendations.push('Recognize and reward achievements to maintain motivation and encourage continued high performance.');
    }
    
    var recHtml = '';
    recommendations.forEach(function(rec, i) {
        recHtml += '<div class="rec-item"><span class="rec-bullet">' + (i + 1) + '.</span> ' + rec + '</div>';
    });
    recEl.innerHTML = recHtml;
}

// ===== CANVA-STYLE EDITOR =====
var exCanvaActiveTool = 'move';
var exCanvaSelectedEl = null;
var exCanvaDragging = null;
var exCanvaResizing = null;

// Toggle Canva editor mode
function togglePhotoEdit() {
    var photoSquare = document.getElementById('exPhotoSquare');
    var toolbar = document.getElementById('exCanvaToolbar');
    var btn = document.getElementById('exPhotoEditBtn');
    var isActive = photoSquare.classList.toggle('canva-mode');
    toolbar.classList.toggle('show', isActive);
    btn.classList.toggle('active', isActive);
    
    if (!isActive) {
        // Exit canva mode - clear selection
        clearCanvaSelection();
    } else {
        // Initialize positions if not set
        initCanvaPositions();
    }
}

function initCanvaPositions() {
    var square = document.getElementById('exPhotoSquare');
    var els = square.querySelectorAll('.photo-placeholder, .photo-name, .photo-position, .photo-category');
    els.forEach(function(el) {
        if (!el.style.left && !el.style.top) {
            // Set initial absolute position from current layout
            var rect = el.getBoundingClientRect();
            var parentRect = square.getBoundingClientRect();
            el.style.left = (rect.left - parentRect.left) + 'px';
            el.style.top = (rect.top - parentRect.top) + 'px';
            el.style.width = rect.width + 'px';
        }
    });
}

// Set active tool
function setCanvaTool(tool) {
    exCanvaActiveTool = tool;
    document.querySelectorAll('#exCanvaToolbar .tool-btn[data-edit]').forEach(function(b) {
        b.classList.toggle('active', b.dataset.edit === tool);
    });
    clearCanvaSelection();
    var square = document.getElementById('exPhotoSquare');
    if (tool === 'resize') {
        square.classList.add('canva-resize-mode');
    } else {
        square.classList.remove('canva-resize-mode');
    }
}

// Select element on click
function selectCanvaElement(el) {
    clearCanvaSelection();
    exCanvaSelectedEl = el;
    el.classList.add('canva-selected');
    
    var square = document.getElementById('exPhotoSquare');
    if (exCanvaActiveTool === 'resize') {
        // Show resize handles
        square.querySelectorAll('.ex-resize-handle').forEach(function(h) {
            h.classList.add('show');
        });
    } else {
        square.querySelectorAll('.ex-resize-handle').forEach(function(h) {
            h.classList.remove('show');
        });
    }
    
    // Load font/color into toolbar based on selected element
    var fontEl = document.getElementById('exCanvaFont');
    var colorEl = document.getElementById('exCanvaColor');
    if (el.classList.contains('photo-name') || el.classList.contains('photo-position') || 
        el.classList.contains('photo-category')) {
        fontEl.value = el.style.fontFamily || '';
        colorEl.value = rgbToHex(el.style.color || getComputedStyle(el).color);
    } else {
        colorEl.value = '#ffffff';
    }
}

function clearCanvaSelection() {
    if (exCanvaSelectedEl) {
        exCanvaSelectedEl.classList.remove('canva-selected');
    }
    exCanvaSelectedEl = null;
    document.querySelectorAll('#exPhotoSquare .ex-resize-handle').forEach(function(h) {
        h.classList.remove('show');
    });
}

// ===== DRAG Elements =====
function startCanvaDrag(e, el) {
    if (exCanvaActiveTool === 'text') {
        // Text tool - make editable
        makeTextEditable(el);
        return;
    }
    
    selectCanvaElement(el);
    if (exCanvaActiveTool === 'resize') return;
    
    exCanvaDragging = {
        el: el,
        startX: e.clientX,
        startY: e.clientY,
        origLeft: parseFloat(el.style.left || 0),
        origTop: parseFloat(el.style.top || 0)
    };
    e.preventDefault();
}

function moveCanvaDrag(e) {
    if (!exCanvaDragging) return;
    var d = exCanvaDragging;
    var dx = e.clientX - d.startX;
    var dy = e.clientY - d.startY;
    var square = document.getElementById('exPhotoSquare');
    var parentRect = square.getBoundingClientRect();
    
    var newLeft = Math.max(0, Math.min(parentRect.width - d.el.offsetWidth, d.origLeft + dx));
    var newTop = Math.max(0, Math.min(parentRect.height - d.el.offsetHeight, d.origTop + dy));
    
    d.el.style.left = newLeft + 'px';
    d.el.style.top = newTop + 'px';
}

function endCanvaDrag() {
    if (exCanvaDragging) {
        saveCanvaState();
    }
    exCanvaDragging = null;
}

// ===== RESIZE Elements =====
function startCanvaResize(e, handle) {
    if (!exCanvaSelectedEl || exCanvaActiveTool !== 'resize') return;
    
    var el = exCanvaSelectedEl;
    exCanvaResizing = {
        el: el,
        handle: handle.dataset.handle,
        startX: e.clientX,
        startY: e.clientY,
        origWidth: el.offsetWidth,
        origHeight: el.offsetHeight,
        origLeft: parseFloat(el.style.left || 0),
        origTop: parseFloat(el.style.top || 0)
    };
    e.preventDefault();
}

function moveCanvaResize(e) {
    if (!exCanvaResizing) return;
    var r = exCanvaResizing;
    var dx = e.clientX - r.startX;
    var dy = e.clientY - r.startY;
    var square = document.getElementById('exPhotoSquare');
    var parentRect = square.getBoundingClientRect();
    
    var newWidth = r.origWidth;
    var newHeight = r.origHeight;
    var newLeft = r.origLeft;
    var newTop = r.origTop;
    
    // Horizontal handles
    if (r.handle.indexOf('e') !== -1) newWidth = r.origWidth + dx;
    if (r.handle.indexOf('w') !== -1) {
        newWidth = r.origWidth - dx;
        newLeft = r.origLeft + dx;
    }
    // Vertical handles
    if (r.handle.indexOf('s') !== -1) newHeight = r.origHeight + dy;
    if (r.handle.indexOf('n') !== -1) {
        newHeight = r.origHeight - dy;
        newTop = r.origTop + dy;
    }
    
    // Keep minimum size
    newWidth = Math.max(30, newWidth);
    newHeight = Math.max(30, newHeight);
    
    // Keep within parent bounds
    if (newLeft + newWidth > parentRect.width) newWidth = parentRect.width - newLeft;
    if (newTop + newHeight > parentRect.height) newHeight = parentRect.height - newTop;
    
    r.el.style.width = newWidth + 'px';
    r.el.style.height = newHeight + 'px';
    r.el.style.left = newLeft + 'px';
    r.el.style.top = newTop + 'px';
}

function endCanvaResize() {
    if (exCanvaResizing) {
        saveCanvaState();
    }
    exCanvaResizing = null;
}

// ===== TEXT EDITING =====
function makeTextEditable(el) {
    if (!el.classList.contains('photo-name') && !el.classList.contains('photo-position') &&
        !el.classList.contains('photo-category')) {
        return; // Only text elements
    }
    
    // Make contenteditable
    el.contentEditable = 'true';
    el.focus();
    
    // Place cursor at end
    var range = document.createRange();
    range.selectNodeContents(el);
    range.collapse(false);
    var sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);
    
    // Remove contenteditable on blur
    el.addEventListener('blur', function handler() {
        el.contentEditable = 'false';
        el.removeEventListener('blur', handler);
        saveCanvaState();
    });
}

// ===== FONT & COLOR =====
function applyCanvaFont() {
    if (!exCanvaSelectedEl) return;
    var font = document.getElementById('exCanvaFont').value;
    exCanvaSelectedEl.style.fontFamily = font || '';
    saveCanvaState();
}

function applyCanvaColor() {
    if (!exCanvaSelectedEl) return;
    var color = document.getElementById('exCanvaColor').value;
    exCanvaSelectedEl.style.color = color;
    saveCanvaState();
}

function rgbToHex(rgb) {
    var match = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    if (!match) return '#ffffff';
    function hex(x) {
        return ('0' + parseInt(x).toString(16)).slice(-2);
    }
    return '#' + hex(match[1]) + hex(match[2]) + hex(match[3]);
}

// ===== LAYER ORDER =====
function bringCanvaElement(direction) {
    if (!exCanvaSelectedEl) return;
    var square = document.getElementById('exPhotoSquare');
    var els = Array.from(square.children).filter(function(c) {
        return c.classList.contains('photo-placeholder') || c.classList.contains('photo-name') ||
               c.classList.contains('photo-position') || c.classList.contains('photo-category');
    });
    
    var idx = els.indexOf(exCanvaSelectedEl);
    if (direction === 'front' && idx < els.length - 1) {
        exCanvaSelectedEl.style.zIndex = parseInt(exCanvaSelectedEl.style.zIndex || 10) + 1;
    } else if (direction === 'back' && idx > 0) {
        exCanvaSelectedEl.style.zIndex = parseInt(exCanvaSelectedEl.style.zIndex || 10) - 1;
    }
    saveCanvaState();
}

// ===== DELETE ELEMENT =====
function deleteCanvaElement() {
    if (!exCanvaSelectedEl) return;
    // Don't allow deleting the photo
    if (exCanvaSelectedEl.classList.contains('photo-placeholder')) {
        alert('Cannot delete the photo!');
        return;
    }
    exCanvaSelectedEl.remove();
    clearCanvaSelection();
    saveCanvaState();
}

// ===== SAVE & RESTORE =====
// Save ONLY layout/styles (position, size, color, font, zIndex)
// NOT text content - so participant data always shows correctly
function saveCanvaState() {
    var square = document.getElementById('exPhotoSquare');
    var els = square.querySelectorAll('.photo-placeholder, .photo-name, .photo-position, .photo-category');
    var state = {};
    els.forEach(function(el) {
        state[el.id] = {
            left: el.style.left,
            top: el.style.top,
            width: el.style.width,
            height: el.style.height,
            zIndex: el.style.zIndex,
            color: el.style.color,
            fontFamily: el.style.fontFamily,
            fontSize: el.style.fontSize,
            display: el.style.display
        };
    });
    localStorage.setItem('ex_canva_state', JSON.stringify(state));
}

function restoreCanvaState() {
    var saved = localStorage.getItem('ex_canva_state');
    if (!saved) return;
    try {
        var state = JSON.parse(saved);
        var square = document.getElementById('exPhotoSquare');
        var els = square.querySelectorAll('.photo-placeholder, .photo-name, .photo-position, .photo-category');
        
        els.forEach(function(el) {
            var savedState = state[el.id];
            if (savedState) {
                // Restore position and size ONLY (not text)
                // Do NOT restore left/top for name - must stay centered at CSS default (top:20rem; left:50%)
                if (savedState.left && el.id !== 'exPhotoName') el.style.left = savedState.left;
                if (savedState.top && el.id !== 'exPhotoName') el.style.top = savedState.top;
                // Do NOT restore width for name - must stay at red grid width (auto-fit controls it)
                if (savedState.width && el.id !== 'exPhotoName') el.style.width = savedState.width;
                // Do NOT restore height for name - must stay at CSS default (auto height)
                if (savedState.height && el.id !== 'exPhotoName') el.style.height = savedState.height;
                if (savedState.zIndex) el.style.zIndex = savedState.zIndex;
                if (savedState.color) el.style.color = savedState.color;
                if (savedState.fontFamily) el.style.fontFamily = savedState.fontFamily;
                // Do NOT restore fontSize for name - auto-fit controls it
                if (savedState.fontSize && el.id !== 'exPhotoName') el.style.fontSize = savedState.fontSize;
                if (savedState.display) el.style.display = savedState.display;
            }
        });
    } catch(e) {
        console.error('Restore canva state error:', e);
    }
}

function exitCanvaMode() {
    var photoSquare = document.getElementById('exPhotoSquare');
    var toolbar = document.getElementById('exCanvaToolbar');
    var btn = document.getElementById('exPhotoEditBtn');
    photoSquare.classList.remove('canva-mode');
    toolbar.classList.remove('show');
    btn.classList.remove('active');
    clearCanvaSelection();
    saveCanvaState();
}

// Restore saved photo settings on page load
function restorePhotoSettings() {
    restoreCanvaState();
}

// ===== RESET ALL =====
function exResetAll() {
    var nameEl = document.getElementById('exNameFilter');
    if (nameEl) {
        nameEl.value = '';
    }
    document.getElementById('exDetailTableWrap').style.display = 'none';
    document.getElementById('exAiSection').classList.remove('show');
    document.getElementById('exEmptyState').style.display = 'flex';
    document.getElementById('exDetailBody').innerHTML = '';
    document.getElementById('exAiMetrics').innerHTML = '';
    document.getElementById('exAiFindingsText').innerHTML = 'Select a dashboard and category to view participant data, photo, and AI-generated performance analysis.';
    document.getElementById('exAiRecText').innerHTML = 'AI recommendations will appear once a participant is selected.';
    
    // Reset photo square
    document.getElementById('exPhotoPlaceholder').innerHTML = '<span id="exPhotoIcon" style="font-size:2.8rem;color:rgba(255,255,255,0.4);">📷</span>';
    document.getElementById('exPhotoPlaceholder').style.display = 'flex';
    var resetNameEl = document.getElementById('exPhotoName');
    resetNameEl.textContent = '—';
    // Reset name position to CSS default (centered) so it never stays in a dragged/down position
    resetNameEl.style.left = '';
    resetNameEl.style.top = '';
    resetNameEl.style.transform = '';
    document.getElementById('exPhotoPosition').textContent = '—';
    document.getElementById('exPhotoSquare').className = 'ex-photo-square';
    document.getElementById('exPhotoCategory').textContent = '—';
}

// ===== DARK MODE TOGGLE =====
function toggleExecutiveDarkMode() {
    var body = document.body;
    var btn = document.getElementById('exDarkModeToggle');
    body.classList.toggle('ex-dark-mode');
    
    // Update button icon/text
    if (body.classList.contains('ex-dark-mode')) {
        btn.textContent = '☀️';
        btn.style.background = '#f59e0b';
        btn.onmouseover = function() { this.style.background = '#d97706'; };
        btn.onmouseout = function() { this.style.background = '#f59e0b'; };
        localStorage.setItem('ex_dark_mode', 'enabled');
    } else {
        btn.textContent = '🌙';
        btn.style.background = '#374151';
        btn.onmouseover = function() { this.style.background = '#1f2937'; };
        btn.onmouseout = function() { this.style.background = '#374151'; };
        localStorage.setItem('ex_dark_mode', 'disabled');
    }
}

// Restore dark mode state on page load
function restoreDarkModeState() {
    var savedState = localStorage.getItem('ex_dark_mode');
    if (savedState === 'enabled') {
        var body = document.body;
        var btn = document.getElementById('exDarkModeToggle');
        body.classList.add('ex-dark-mode');
        if (btn) {
            btn.textContent = '☀️';
            btn.style.background = '#f59e0b';
            btn.onmouseover = function() { this.style.background = '#d97706'; };
            btn.onmouseout = function() { this.style.background = '#f59e0b'; };
        }
    }
}

// ===== INIT =====
document.addEventListener('DOMContentLoaded', function() {
    restoreDarkModeState();
    restorePhotoSettings();
    
    detectAndLoadData();
    
    // Initialize AI character drag functionality
    initAiCharacterDrag();
    
    // Listen for refresh button
    var refreshBtn = document.querySelector('.exec-top-btn[data-action="refresh"]');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            loadExecutiveData();
        });
    }
    
    // Listen for month/year changes
    var monthSelect = document.getElementById('exMonth');
    var yearSelect = document.getElementById('exYear');
    if (monthSelect) monthSelect.addEventListener('change', function() { loadExecutiveData(); exLoadParticipantNames(); });
    if (yearSelect) yearSelect.addEventListener('change', function() { loadExecutiveData(); exLoadParticipantNames(); });
    
    // ===== CANVA EDITOR EVENT WIRING =====
    var square = document.getElementById('exPhotoSquare');
    if (square) {
        // Click/drag on elements
        var draggableSelector = '.photo-placeholder, .photo-name, .photo-position, .photo-category';
        
        square.addEventListener('mousedown', function(e) {
            // Only when in canva mode
            if (!square.classList.contains('canva-mode')) return;
            
            // Check if clicking on resize handle
            var handle = e.target.closest('.ex-resize-handle');
            if (handle) {
                startCanvaResize(e, handle);
                return;
            }
            
            // Check if clicking on draggable element
            var el = e.target.closest(draggableSelector);
            if (el) {
                startCanvaDrag(e, el);
            }
        });
        
        document.addEventListener('mousemove', function(e) {
            if (exCanvaResizing) {
                moveCanvaResize(e);
            } else if (exCanvaDragging) {
                moveCanvaDrag(e);
            }
        });
        
        document.addEventListener('mouseup', function() {
            if (exCanvaResizing) {
                endCanvaResize();
            } else if (exCanvaDragging) {
                endCanvaDrag();
            }
        });
        
        // Resize handle events
        square.querySelectorAll('.ex-resize-handle').forEach(function(handle) {
            handle.addEventListener('mousedown', function(e) {
                e.stopPropagation();
                if (square.classList.contains('canva-mode')) {
                    startCanvaResize(e, handle);
                }
            });
        });
        
        // Prevent text selection in canva mode
        square.addEventListener('selectstart', function(e) {
            if (square.classList.contains('canva-mode')) {
                e.preventDefault();
            }
        });
    }
});
</script>
<?= $this->endSection() ?>