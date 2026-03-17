@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Admin Dashboard</h1>
    </div>

    <div>
        <p style="margin-bottom: 20px;">Overview of badge registrations and printing activity.</p>

        <!-- Printing Statistics Section -->
        <div style="margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 20px; margin-bottom: 0; color: #1f2937;">Printing Statistics</h2>
                <button onclick="openPrintingReportModal()" class="btn btn-primary" style="padding: 10px 20px; font-size: 14px;">
                    Download Printing Report
                </button>
            </div>

            <!-- KPI Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 30px;">
                <div class="card" style="margin-bottom: 0; padding: 16px; border-left: 4px solid #3b82f6;">
                    <h3 style="font-size: 13px; color: #6b7280; text-transform: uppercase;">Total Registrations</h3>
                    <p style="font-size: 26px; font-weight: 600; margin-top: 4px;">{{ $totalRegistrations }}</p>
                </div>
                <div class="card" style="margin-bottom: 0; padding: 16px; border-left: 4px solid #10b981;">
                    <h3 style="font-size: 13px; color: #6b7280; text-transform: uppercase;">Users Printed</h3>
                    <p style="font-size: 26px; font-weight: 600; margin-top: 4px;">{{ $totalPrinted }}</p>
                    <p style="font-size: 12px; color: #6b7280;">Total Prints: {{ $totalPrints }} | Unique: {{ $totalUniquePrinted }} | Duplicate: {{ $totalDuplicate }}</p>
                </div>
                <div class="card" style="margin-bottom: 0; padding: 16px; border-left: 4px solid #f59e0b;">
                    <h3 style="font-size: 13px; color: #6b7280; text-transform: uppercase;">Not Printed</h3>
                    <p style="font-size: 26px; font-weight: 600; margin-top: 4px;">{{ $totalNotPrinted }}</p>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 30px;">
            <!-- Category wise pie -->
            <div class="card" style="margin-bottom: 0;">
                <h3 style="font-size: 16px; margin-bottom: 10px;">Category-wise Registrations</h3>
                <canvas id="categoryPieChart" height="200"></canvas>
            </div>

            <!-- Day wise line -->
            <div class="card" style="margin-bottom: 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <h3 style="font-size: 16px; margin-bottom: 0;">Day-wise Printed (Last 14 days)</h3>
                    <select id="dayLineChartFilter" style="padding: 5px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                        <option value="">All Categories</option>
                        @foreach($categoriesList as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <canvas id="dayLineChart" height="200"></canvas>
            </div>

            <!-- Day + Category stacked -->
            <div class="card" style="margin-bottom: 0;">
                <h3 style="font-size: 16px; margin-bottom: 10px;">Day &amp; Category-wise Printed</h3>
                <canvas id="dayCategoryBarChart" height="220"></canvas>
            </div>
        </div>

        <!-- Category table -->
        <div class="card" style="margin-bottom: 0;">
            <h3 style="font-size: 16px; margin-bottom: 10px;">Category Summary</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background: #eff6ff;">
                            <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: left;">Category</th>
                            <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: right;">Total</th>
                            <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: right;">Printed</th>
                            <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: right;">Not Printed</th>
                            <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: right;">Unique</th>
                            <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: right;">Duplicate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categoryStats as $row)
                            <tr>
                                <td style="padding: 8px; border-bottom: 1px solid #f3f4f6;">{{ $row->Category }}</td>
                                <td style="padding: 8px; border-bottom: 1px solid #f3f4f6; text-align: right;">{{ $row->total }}</td>
                                <td style="padding: 8px; border-bottom: 1px solid #f3f4f6; text-align: right;">{{ $row->printed }}</td>
                                <td style="padding: 8px; border-bottom: 1px solid #f3f4f6; text-align: right;">{{ $row->not_printed }}</td>
                                <td style="padding: 8px; border-bottom: 1px solid #f3f4f6; text-align: right;">{{ $row->unique_printed }}</td>
                                <td style="padding: 8px; border-bottom: 1px solid #f3f4f6; text-align: right;">{{ $row->duplicate_printed }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 12px; text-align: center; color: #6b7280;">No data yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Scanning Statistics Section -->
        <div style="margin-top: 40px; border-top: 2px solid #e5e7eb; padding-top: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 20px; margin-bottom: 0; color: #1f2937;">Scanning Statistics</h2>
                <button onclick="openScanningReportModal()" class="btn btn-primary" style="padding: 10px 20px; font-size: 14px;">
                    Download Scanning Report
                </button>
            </div>

            <!-- Scanning KPI Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 30px;">
                <div class="card" style="margin-bottom: 0; padding: 16px; border-left: 4px solid #6366f1;">
                    <h3 style="font-size: 13px; color: #6b7280; text-transform: uppercase;">Total Scans</h3>
                    <p style="font-size: 26px; font-weight: 600; margin-top: 4px;">{{ $totalScans }}</p>
                    <p style="font-size: 12px; color: #6b7280;">Unique: {{ $uniqueScans }} | Duplicate: {{ $duplicateScans }}</p>
                </div>
                <div class="card" style="margin-bottom: 0; padding: 16px; border-left: 4px solid #10b981;">
                    <h3 style="font-size: 13px; color: #6b7280; text-transform: uppercase;">Allowed</h3>
                    <p style="font-size: 26px; font-weight: 600; margin-top: 4px;">{{ $totalAllowed }}</p>
                    <p style="font-size: 12px; color: #6b7280;">{{ $totalScans > 0 ? number_format(($totalAllowed / $totalScans) * 100, 1) : 0 }}%</p>
                </div>
                <div class="card" style="margin-bottom: 0; padding: 16px; border-left: 4px solid #ef4444;">
                    <h3 style="font-size: 13px; color: #6b7280; text-transform: uppercase;">Denied</h3>
                    <p style="font-size: 26px; font-weight: 600; margin-top: 4px;">{{ $totalDenied }}</p>
                    <p style="font-size: 12px; color: #6b7280;">{{ $totalScans > 0 ? number_format(($totalDenied / $totalScans) * 100, 1) : 0 }}%</p>
                </div>
            </div>

            <!-- Scanning Charts -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 30px;">
                <!-- Location-wise scanning pie -->
                <div class="card" style="margin-bottom: 0;">
                    <h3 style="font-size: 16px; margin-bottom: 10px;">Location-wise Scans</h3>
                    <canvas id="locationScanPieChart" height="200"></canvas>
                </div>

                <!-- Day-wise scanning line -->
                <div class="card" style="margin-bottom: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h3 style="font-size: 16px; margin-bottom: 0;">Day-wise Scans (Last 14 days)</h3>
                        <select id="dayScanLineChartFilter" style="padding: 5px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                            <option value="">All Categories</option>
                            @foreach($categoriesList as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <canvas id="dayScanLineChart" height="200"></canvas>
                </div>

                <!-- Location + Day stacked -->
                <div class="card" style="margin-bottom: 0;">
                    <h3 style="font-size: 16px; margin-bottom: 10px;">Location &amp; Day-wise Scans</h3>
                    <canvas id="locationDayScanBarChart" height="220"></canvas>
                </div>
            </div>

            <!-- Location-wise Scanning Table -->
            <div class="card" style="margin-bottom: 0;">
                <h3 style="font-size: 16px; margin-bottom: 10px;">Location-wise Scanning Summary</h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #eff6ff;">
                                <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: left;">Location</th>
                                <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: right;">Total Scans</th>
                                <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: right;">Allowed</th>
                                <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: right;">Denied</th>
                                <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: right;">Unique RegIDs</th>
                                <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: right;">Duplicate Scans</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($locationStats as $row)
                                <tr>
                                    <td style="padding: 8px; border-bottom: 1px solid #f3f4f6;">{{ $row->location_name }}</td>
                                    <td style="padding: 8px; border-bottom: 1px solid #f3f4f6; text-align: right;">{{ $row->total_scans }}</td>
                                    <td style="padding: 8px; border-bottom: 1px solid #f3f4f6; text-align: right; color: #10b981;">{{ $row->allowed }}</td>
                                    <td style="padding: 8px; border-bottom: 1px solid #f3f4f6; text-align: right; color: #ef4444;">{{ $row->denied }}</td>
                                    <td style="padding: 8px; border-bottom: 1px solid #f3f4f6; text-align: right;">{{ $row->unique_regids }}</td>
                                    <td style="padding: 8px; border-bottom: 1px solid #f3f4f6; text-align: right; color: #f59e0b;">{{ $row->duplicate_scans }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 12px; text-align: center; color: #6b7280;">No scanning data yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const categoryStats = @json($categoryStats);
    const dayWise       = @json($dayWise);
    const dayCategory   = @json($dayCategory);
    const locationStats  = @json($locationStats);
    const dayWiseScans   = @json($dayWiseScans);
    const dayCategoryScans = @json($dayCategoryScans);
    const locationDayScans = @json($locationDayScans);

    // Create consistent color mapping for categories
    const palette = ['#3b82f6','#10b981','#f59e0b','#ef4444','#6366f1','#ec4899','#22c55e','#0ea5e9','#8b5cf6','#f97316'];
    
    // Get all unique categories from all data sources and sort them for consistency
    const allCategories = [
        ...new Set([
            ...categoryStats.map(c => c.Category),
            ...dayCategory.map(d => d.category || d.Category).filter(c => c),
            ...(dayCategoryScans || []).map(d => d.category).filter(c => c)
        ])
    ].filter(c => c && c !== 'null' && c !== 'undefined' && c !== '').sort();
    
    // Create consistent color map for categories (sorted order ensures consistency)
    const categoryColorMap = {};
    allCategories.forEach((cat, idx) => {
        categoryColorMap[cat] = palette[idx % palette.length];
    });
    
    // Function to get color for a category
    function getCategoryColor(category) {
        if (!category) return palette[0];
        return categoryColorMap[category] || palette[0];
    }

    // Category-wise pie chart
    const catLabels = categoryStats.map(c => c.Category);
    const catTotals = categoryStats.map(c => c.total);

    if (document.getElementById('categoryPieChart')) {
        new Chart(document.getElementById('categoryPieChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catTotals,
                    backgroundColor: catLabels.map(cat => getCategoryColor(cat)),
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Day-wise line chart with category filter and enhanced tooltips
    let dayLineChartInstance = null;
    const allDayWiseData = dayWise;
    const allDayCategoryData = dayCategory;

    function updateDayLineChart(selectedCategory = '') {
        let filteredData = allDayWiseData;
        
        if (selectedCategory) {
            // Filter by category
            const categoryData = allDayCategoryData.filter(d => {
                const cat = d.category || d.Category;
                return cat === selectedCategory;
            });
            
            // Group by date
            const dateMap = {};
            categoryData.forEach(d => {
                const date = d.date;
                if (!dateMap[date]) {
                    dateMap[date] = { printed_total: 0, unique_printed: 0, duplicate_printed: 0 };
                }
                dateMap[date].printed_total += d.printed_total || 0;
                dateMap[date].unique_printed += d.unique_printed || 0;
                dateMap[date].duplicate_printed += d.duplicate_printed || 0;
            });
            
            filteredData = Object.keys(dateMap).map(date => ({
                date: date,
                printed_total: dateMap[date].printed_total,
                unique_printed: dateMap[date].unique_printed,
                duplicate_printed: dateMap[date].duplicate_printed
            }));
        }
        
        const dayLabels = filteredData.map(d => d.date);
        const dayValues = filteredData.map(d => d.printed_total);
        const dayUnique = filteredData.map(d => d.unique_printed || 0);
        const dayDuplicate = filteredData.map(d => d.duplicate_printed || 0);

        if (dayLineChartInstance) {
            dayLineChartInstance.destroy();
        }

        if (document.getElementById('dayLineChart')) {
            dayLineChartInstance = new Chart(document.getElementById('dayLineChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: dayLabels,
                    datasets: [{
                        label: 'Printed',
                        data: dayValues,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.2)',
                        tension: 0.3,
                        fill: true,
                    }]
                },
                options: {
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                afterLabel: function(context) {
                                    const index = context.dataIndex;
                                    const unique = dayUnique[index] || 0;
                                    const duplicate = dayDuplicate[index] || 0;
                                    return `Unique: ${unique} | Duplicate: ${duplicate}`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Initialize day line chart
    updateDayLineChart();

    // Category filter change handler
    const dayLineChartFilter = document.getElementById('dayLineChartFilter');
    if (dayLineChartFilter) {
        dayLineChartFilter.addEventListener('change', function() {
            updateDayLineChart(this.value);
        });
    }

    // Day + category stacked bar chart
    // Filter out null/undefined categories and handle both 'category' and 'Category' property names
    const dayCategoryFiltered = dayCategory.filter(d => {
        const cat = d.category || d.Category;
        return cat && cat !== null && cat !== 'null' && cat !== 'undefined' && cat !== '';
    });
    
    // Get unique category names (handle both property name variations)
    const categoryNames = [...new Set(dayCategoryFiltered.map(d => {
        const cat = d.category || d.Category;
        return cat || 'Unknown';
    }))].filter(cat => cat && cat !== 'null' && cat !== 'undefined' && cat !== 'Unknown');
    
    // Get unique dates and sort them
    const dayCatLabels = [...new Set(dayCategoryFiltered.map(d => d.date))].sort();

    // Day + Category chart (no filter - shows all categories)
    const datasets = categoryNames.map((cat) => {
        return {
            label: cat,
            data: dayCatLabels.map(date => {
                const rec = dayCategoryFiltered.find(d => {
                    const recDate = d.date;
                    const recCategory = d.category || d.Category;
                    return recDate === date && recCategory === cat;
                });
                return rec ? (rec.printed_total || 0) : 0;
            }),
            backgroundColor: getCategoryColor(cat),
            stack: 'stack1',
            uniqueData: dayCatLabels.map(date => {
                const rec = dayCategoryFiltered.find(d => {
                    const recDate = d.date;
                    const recCategory = d.category || d.Category;
                    return recDate === date && recCategory === cat;
                });
                return rec ? (rec.unique_printed || 0) : 0;
            }),
            duplicateData: dayCatLabels.map(date => {
                const rec = dayCategoryFiltered.find(d => {
                    const recDate = d.date;
                    const recCategory = d.category || d.Category;
                    return recDate === date && recCategory === cat;
                });
                return rec ? (rec.duplicate_printed || 0) : 0;
            }),
        };
    });

    if (document.getElementById('dayCategoryBarChart')) {
        if (dayCatLabels.length > 0 && categoryNames.length > 0 && datasets.length > 0) {
            new Chart(document.getElementById('dayCategoryBarChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: dayCatLabels,
                    datasets: datasets,
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { stacked: true },
                        y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } }
                    },
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                afterLabel: function(context) {
                                    const datasetIndex = context.datasetIndex;
                                    const dataIndex = context.dataIndex;
                                    const dataset = datasets[datasetIndex];
                                    const unique = dataset.uniqueData[dataIndex] || 0;
                                    const duplicate = dataset.duplicateData[dataIndex] || 0;
                                    return `Unique: ${unique} | Duplicate: ${duplicate}`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Location-wise scanning pie chart
    // Create consistent color map for locations
    const locationColorMap = {};
    if (locationStats && locationStats.length > 0) {
        const locLabels = locationStats.map(l => l.location_name);
        locLabels.forEach((loc, idx) => {
            locationColorMap[loc] = palette[idx % palette.length];
        });
    }
    
    function getLocationColor(location) {
        return locationColorMap[location] || palette[0];
    }
    
    if (locationStats && locationStats.length > 0 && document.getElementById('locationScanPieChart')) {
        const locLabels = locationStats.map(l => l.location_name);
        const locTotals = locationStats.map(l => l.total_scans);
        
        new Chart(document.getElementById('locationScanPieChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: locLabels,
                datasets: [{
                    data: locTotals,
                    backgroundColor: locLabels.map(loc => getLocationColor(loc)),
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Day-wise scanning line chart with category filter and enhanced tooltips
    let dayScanLineChartInstance = null;
    const allDayWiseScansData = dayWiseScans;
    const allDayCategoryScansData = dayCategoryScans || [];

    function updateDayScanLineChart(selectedCategory = '') {
        let filteredData = allDayWiseScansData;
        
        if (selectedCategory && allDayCategoryScansData.length > 0) {
            // Filter by category
            const categoryData = allDayCategoryScansData.filter(d => {
                const cat = d.category;
                return cat === selectedCategory;
            });
            
            // Group by date
            const dateMap = {};
            categoryData.forEach(d => {
                const date = d.date;
                if (!dateMap[date]) {
                    dateMap[date] = { total: 0, allowed: 0, denied: 0 };
                }
                dateMap[date].total += d.total || 0;
                dateMap[date].allowed += d.allowed || 0;
                dateMap[date].denied += d.denied || 0;
            });
            
            filteredData = Object.keys(dateMap).map(date => ({
                date: date,
                total: dateMap[date].total,
                allowed: dateMap[date].allowed,
                denied: dateMap[date].denied
            }));
        }
        
        const dayScanLabels = filteredData.map(d => d.date);
        const dayScanAllowed = filteredData.map(d => d.allowed || 0);
        const dayScanDenied = filteredData.map(d => d.denied || 0);
        const dayScanTotal = filteredData.map(d => d.total || 0);

        if (dayScanLineChartInstance) {
            dayScanLineChartInstance.destroy();
        }

        if (document.getElementById('dayScanLineChart')) {
            dayScanLineChartInstance = new Chart(document.getElementById('dayScanLineChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: dayScanLabels,
                    datasets: [{
                        label: 'Allowed',
                        data: dayScanAllowed,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.2)',
                        tension: 0.3,
                        fill: true,
                    }, {
                        label: 'Denied',
                        data: dayScanDenied,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239,68,68,0.2)',
                        tension: 0.3,
                        fill: true,
                    }]
                },
                options: {
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    },
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                footer: function(tooltipItems) {
                                    const index = tooltipItems[0].dataIndex;
                                    const total = dayScanTotal[index] || 0;
                                    const allowed = dayScanAllowed[index] || 0;
                                    const denied = dayScanDenied[index] || 0;
                                    return `Total: ${total} | Approved: ${allowed} | Rejected: ${denied}`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Initialize day scan line chart
    updateDayScanLineChart();

    // Category filter change handler for day scan chart
    const dayScanLineChartFilter = document.getElementById('dayScanLineChartFilter');
    if (dayScanLineChartFilter) {
        dayScanLineChartFilter.addEventListener('change', function() {
            updateDayScanLineChart(this.value);
        });
    }

    // Location + Day scanning stacked bar chart with enhanced tooltips
    if (locationDayScans && locationDayScans.length > 0 && document.getElementById('locationDayScanBarChart')) {
        const locationNames = [...new Set(locationDayScans.map(d => d.location_name))];
        const locationDayLabels = [...new Set(locationDayScans.map(d => d.date))];

        const locationDatasets = locationNames.map((loc) => {
            return {
                label: loc,
                data: locationDayLabels.map(date => {
                    const rec = locationDayScans.find(d => d.date === date && d.location_name === loc);
                    return rec ? rec.total : 0;
                }),
                backgroundColor: getLocationColor(loc),
                stack: 'stack1',
                // Store approved/rejected data for tooltips
                allowedData: locationDayLabels.map(date => {
                    const rec = locationDayScans.find(d => d.date === date && d.location_name === loc);
                    return rec ? (rec.allowed || 0) : 0;
                }),
                deniedData: locationDayLabels.map(date => {
                    const rec = locationDayScans.find(d => d.date === date && d.location_name === loc);
                    return rec ? (rec.denied || 0) : 0;
                }),
            };
        });

        new Chart(document.getElementById('locationDayScanBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: locationDayLabels,
                datasets: locationDatasets,
            },
            options: {
                responsive: true,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } }
                },
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            afterLabel: function(context) {
                                const datasetIndex = context.datasetIndex;
                                const dataIndex = context.dataIndex;
                                const dataset = locationDatasets[datasetIndex];
                                const allowed = dataset.allowedData[dataIndex] || 0;
                                const denied = dataset.deniedData[dataIndex] || 0;
                                return `Approved: ${allowed} | Rejected: ${denied}`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Modal functions
    function openScanningReportModal() {
        document.getElementById('scanningReportModal').style.display = 'flex';
    }

    function closeScanningReportModal() {
        document.getElementById('scanningReportModal').style.display = 'none';
    }

    function openPrintingReportModal() {
        document.getElementById('printingReportModal').style.display = 'flex';
    }

    function closePrintingReportModal() {
        document.getElementById('printingReportModal').style.display = 'none';
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const scanningModal = document.getElementById('scanningReportModal');
        const printingModal = document.getElementById('printingReportModal');
        if (event.target == scanningModal) {
            scanningModal.style.display = 'none';
        }
        if (event.target == printingModal) {
            printingModal.style.display = 'none';
        }
    }
</script>
@endpush

<!-- Scanning Report Modal -->
<div id="scanningReportModal" class="report-modal" style="display: none;">
    <div class="report-modal-content">
        <div class="report-modal-header">
            <h2>Download Scanning Report</h2>
            <span class="report-modal-close" onclick="closeScanningReportModal()">&times;</span>
        </div>
        <form action="{{ route('admin.reports.scanning.download') }}" method="GET" id="scanningReportForm">
            <div class="report-modal-body">
                <div class="form-group">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <select name="location_id" class="form-control">
                        <option value="">All Locations</option>
                        @foreach(\App\Models\Location::where('is_active', true)->get() as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="allowed">Allowed Only</option>
                        <option value="denied">Denied Only</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        @foreach(\App\Models\Category::all() as $category)
                            <option value="{{ $category->Category }}">{{ $category->Category }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="report-modal-footer">
                <button type="button" onclick="closeScanningReportModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Download Excel</button>
            </div>
        </form>
    </div>
</div>

<!-- Printing Report Modal -->
<div id="printingReportModal" class="report-modal" style="display: none;">
    <div class="report-modal-content">
        <div class="report-modal-header">
            <h2>Download Printing Report</h2>
            <span class="report-modal-close" onclick="closePrintingReportModal()">&times;</span>
        </div>
        <form action="{{ route('admin.reports.printing.download') }}" method="GET" id="printingReportForm">
            <div class="report-modal-body">
                <div class="form-group">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        @foreach(\App\Models\Category::all() as $category)
                            <option value="{{ $category->Category }}">{{ $category->Category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Print Type</label>
                    <select name="print_type" class="form-control">
                        <option value="">All Types</option>
                        <option value="single">Single Print</option>
                        <option value="bulk">Bulk Print</option>
                    </select>
                </div>
            </div>
            <div class="report-modal-footer">
                <button type="button" onclick="closePrintingReportModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Download Excel</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    /* Report Modal Styles */
    .report-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
    }

    .report-modal-content {
        background-color: #ffffff;
        margin: auto;
        padding: 0;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .report-modal-header {
        padding: 20px 24px;
        background-color: #1e40af;
        color: white;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .report-modal-header h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
    }

    .report-modal-close {
        color: white;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        line-height: 1;
    }

    .report-modal-close:hover {
        opacity: 0.7;
    }

    .report-modal-body {
        padding: 24px;
    }

    .report-modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }
</style>
@endpush
@endsection
