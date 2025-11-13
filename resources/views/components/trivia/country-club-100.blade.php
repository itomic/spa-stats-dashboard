<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">
            <i class="fas fa-trophy me-2"></i>The 100% Country Club
        </h5>
        
        <p class="text-muted mb-3">
            Countries where we know the court count for all (or most) venues. Once we identify the number of courts in ALL venues, then all countries with squash venues will join the 100% Country Club!
        </p>
        
        <!-- Loading Indicator -->
        <div id="country-club-loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2">Loading country club data...</p>
        </div>
        
        <!-- Table container (hidden initially) -->
        <div id="country-club-container" class="d-none">
            <!-- Continent Filter -->
            <div class="mb-3">
                <select class="form-select form-select-sm w-auto" id="country-club-continent-filter">
                    <option value="">All Continents</option>
                    <option value="1">Africa</option>
                    <option value="2">Asia</option>
                    <option value="3">Europe</option>
                    <option value="4">North America</option>
                    <option value="5">Oceania</option>
                    <option value="6">South America</option>
                </select>
            </div>
            
            <div class="table-responsive" style="max-height: 600px; overflow-y: auto; overflow-x: auto;">
                <table class="table table-hover table-sm" id="country-club-table">
                    <thead class="sticky-top bg-light">
                        <tr>
                            <th class="text-center sticky-col" style="width: 50px;">#</th>
                            <th class="sortable sticky-col sticky-col-country" data-sort="name" style="min-width: 150px;">
                                Country <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th class="text-end sortable" data-sort="total_venues" style="min-width: 80px;">
                                Venues <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th class="text-end sortable" data-sort="venues_with_courts" style="min-width: 120px;">
                                Venues with &gt; 0 Courts <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th class="text-end sortable" data-sort="total_courts" style="min-width: 80px;">
                                Courts <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th class="text-end sortable" data-sort="percentage" style="min-width: 150px;">
                                % of Venues with &gt; 0 Courts <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th class="text-end sortable" data-sort="courts_per_venue" style="min-width: 120px;">
                                Courts Per Venue <i class="fas fa-sort ms-1"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="country-club-tbody">
                        <!-- Populated by JavaScript -->
                    </tbody>
                    <tfoot class="sticky-bottom bg-light fw-bold">
                        <tr>
                            <td colspan="2" class="sticky-col sticky-col-country">Grand Summary:</td>
                            <td class="text-end" id="total-venues">-</td>
                            <td class="text-end" id="total-venues-with-courts">-</td>
                            <td class="text-end" id="total-courts">-</td>
                            <td class="text-end" id="total-percentage">-</td>
                            <td class="text-end" id="avg-courts-per-venue">-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    #country-club-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    
    #country-club-table tfoot td {
        position: sticky;
        bottom: 0;
        z-index: 10;
        background-color: #f8f9fa;
        border-top: 2px solid #dee2e6;
    }
    
    #country-club-table tbody tr:hover {
        background-color: #f1f3f5;
    }
    
    /* Highlight 100% countries with subtle green tint while preserving stripes */
    #country-club-table tbody tr[data-hundred-percent="true"] {
        background-color: #d1f4e0 !important;
    }
    
    #country-club-table tbody tr[data-hundred-percent="true"]:hover {
        background-color: #c0edd4 !important;
    }
    
    /* Sticky first two columns */
    .sticky-col {
        position: sticky;
        left: 0;
        z-index: 11;
    }
    
    .sticky-col-country {
        left: 50px; /* Width of # column */
    }
    
    /* Header sticky columns */
    #country-club-table thead th.sticky-col {
        background-color: #f8f9fa;
    }
    
    /* Regular rows - white background for sticky columns */
    #country-club-table tbody tr td.sticky-col {
        background-color: white;
    }
    
    /* Hover state for all rows */
    #country-club-table tbody tr:hover td.sticky-col {
        background-color: #f1f3f5;
    }
    
    /* 100% countries - green background for sticky columns */
    #country-club-table tbody tr[data-hundred-percent="true"] td.sticky-col {
        background-color: #d1f4e0;
    }
    
    #country-club-table tbody tr[data-hundred-percent="true"]:hover td.sticky-col {
        background-color: #c0edd4;
    }
    
    #country-club-table tfoot td.sticky-col {
        background-color: #f8f9fa;
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

