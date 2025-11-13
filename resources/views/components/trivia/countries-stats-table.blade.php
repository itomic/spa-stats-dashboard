<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">
            <i class="fas fa-table me-2"></i>Squash Venues & Courts by Population & Land Area
        </h5>
        
        <p class="text-muted mb-3">
            Comprehensive statistics for all countries with squash venues, showing venue and court density relative to population and land area.
        </p>
        
        <!-- Loading indicator -->
        <div id="countries-stats-loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2">Loading country statistics...</p>
        </div>
        
        <!-- Table container (hidden initially) -->
        <div id="countries-stats-container" class="d-none">
            <div class="table-responsive" style="max-height: 600px; overflow-y: auto; overflow-x: auto;">
                <table class="table table-striped table-hover table-sm" id="countries-stats-table">
                    <thead class="sticky-top bg-light">
                        <tr>
                            <th class="text-center sticky-col" style="width: 50px;">#</th>
                            <th class="sortable sticky-col sticky-col-country" data-sort="name" style="min-width: 150px;">
                                Country <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th class="text-end sortable" data-sort="population" style="min-width: 100px;">
                                Pop. (millions) <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th class="text-end sortable" data-sort="area_sq_km" style="min-width: 120px;">
                                Area (m. sq km) <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th class="text-end sortable" data-sort="venues" style="min-width: 80px;">
                                Venues <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th class="text-end sortable" data-sort="courts" style="min-width: 80px;">
                                Courts <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th class="text-end sortable" data-sort="venues_per_population" style="min-width: 80px;">
                                V / P <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th class="text-end sortable" data-sort="courts_per_population" style="min-width: 80px;">
                                C / P <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th class="text-end sortable" data-sort="venues_per_area" style="min-width: 80px;">
                                V / A <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th class="text-end sortable" data-sort="courts_per_area" style="min-width: 80px;">
                                C / A <i class="fas fa-sort ms-1"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="countries-stats-tbody">
                        <!-- Populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 small text-muted">
                <strong>Legend:</strong>
                <ul class="mb-0">
                    <li><strong>V / P</strong> = Venues per million population</li>
                    <li><strong>C / P</strong> = Courts per million population</li>
                    <li><strong>V / A</strong> = Venues per 1,000 sq km</li>
                    <li><strong>C / A</strong> = Courts per 1,000 sq km</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    #countries-stats-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    
    #countries-stats-table tbody tr:hover {
        background-color: #f1f3f5;
    }
    
    /* Sticky first two columns */
    .sticky-col {
        position: sticky;
        left: 0;
        background-color: #f8f9fa;
        z-index: 11;
    }
    
    .sticky-col-country {
        left: 50px; /* Width of # column */
    }
    
    #countries-stats-table tbody td.sticky-col {
        background-color: white;
    }
    
    #countries-stats-table tbody tr:hover td.sticky-col {
        background-color: #f1f3f5;
    }
    
    /* Sortable column styling */
    .sortable {
        cursor: pointer;
        user-select: none;
    }
    
    .sortable:hover {
        background-color: #e9ecef;
    }
    
    .sortable i {
        opacity: 0.3;
        font-size: 0.75rem;
    }
    
    .sortable.sort-asc i.fa-sort {
        display: none;
    }
    
    .sortable.sort-asc::after {
        content: '\f0de';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        margin-left: 0.25rem;
        font-size: 0.75rem;
    }
    
    .sortable.sort-desc i.fa-sort {
        display: none;
    }
    
    .sortable.sort-desc::after {
        content: '\f0dd';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        margin-left: 0.25rem;
        font-size: 0.75rem;
    }
</style>

