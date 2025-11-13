<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">
            <i class="fas fa-mountain me-2"></i>High Altitude Squash Venues (2000m+ above sea level)
        </h5>
        
        <!-- Legend -->
        <div class="mb-3 d-flex align-items-center gap-3 flex-wrap">
            <div class="small text-muted">
                <span style="color: #fbbf24;">●</span> 2000-3000m
                <span style="color: #f97316;">●</span> 3000-3500m
                <span style="color: #dc2626;">●</span> 3500m+
            </div>
            <div class="ms-auto">
                <span class="badge bg-primary" id="venues-count">Loading...</span>
            </div>
        </div>
        
        <!-- Map Container -->
        <div id="highest-venues-map" style="height: 600px; border-radius: 0.5rem;"></div>
        
        <!-- Top 10 List (collapsible) -->
        <div class="mt-3">
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#topVenuesList" aria-expanded="false">
                <i class="fas fa-trophy me-1"></i>View Top 10 Highest Venues
            </button>
            <div class="collapse mt-2" id="topVenuesList">
                <div class="card card-body">
                    <div id="top-venues-list-content">
                        <div class="text-center text-muted">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

