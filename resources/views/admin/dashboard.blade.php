@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($stats as $stat)
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover-lift hover-glow-purple transition-all duration-300">
                    <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                        <div class="text-sm font-medium text-muted-foreground">
                            {{ $stat['title'] }}
                        </div>
                        <div class="{{ $stat['color'] }} text-white w-8 h-8 rounded-lg flex items-center justify-center glow-purple">
                            <i class="fas {{ $stat['icon'] }} w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-glow-purple">{{ number_format($stat['value']) }}</div>
                        <div class="text-xs mt-1 {{ $stat['change'] >= 0 ? 'text-success' : 'text-destructive' }}">
                            <i class="fas fa-{{ $stat['change'] >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
                            {{ $stat['change'] >= 0 ? '+' : '' }}{{ $stat['change'] }}% ce mois
                        </div>
                    </div>
                </div>
            @endforeach

            @foreach($cards ?? [] as $card)
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover-lift hover-glow-purple transition-all duration-300">
                    <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                        <div class="text-sm font-medium text-muted-foreground">
                            {{ $card['name'] }}
                        </div>
                        <div class="{{ $card['color'] ?? 'bg-primary' }} text-white w-8 h-8 rounded-lg flex items-center justify-center glow-purple">
                            <i class="fas {{ $card['icon'] ?? 'fa-chart-line' }} w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-glow-purple">{{ $card['value'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-card rounded-xl p-6 border border-border">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-foreground">Visiteurs uniques</h3>
                        <p class="text-sm text-muted-foreground mt-1">
                            Total: <span id="totalVisitors" class="font-semibold text-foreground">-</span>
                            • Moyenne: <span id="avgVisitors" class="font-semibold text-foreground">-</span>/jour
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-xs font-medium bg-primary text-primary-foreground rounded-md transition-colors active-range-btn" data-range="7">7j</button>
                        <button class="px-3 py-1 text-xs font-medium text-muted-foreground hover:text-foreground hover:bg-muted rounded-md transition-colors" data-range="30">30j</button>
                        <button class="px-3 py-1 text-xs font-medium text-muted-foreground hover:text-foreground hover:bg-muted rounded-md transition-colors" data-range="90">90j</button>
                    </div>
                </div>
                <div id="visitorsChart" style="height: 320px;"></div>
            </div>

            <div class="bg-card rounded-xl p-6 border border-border">
                <h3 class="text-lg font-semibold text-foreground mb-4">Activité récente</h3>
                <div class="space-y-4 max-h-[370px] overflow-y-auto custom-scrollbar">
                    @forelse ($recentActivities as $activity)
                        @php
                            $typeColors = [
                                'page' => 'bg-blue-500',
                                'article' => 'bg-green-500',
                                'module' => 'bg-purple-500',
                                'theme' => 'bg-orange-500',
                                'media' => 'bg-cyan-600',
                                'user' => 'bg-pink-500',
                                'settings' => 'bg-red-500',
                            ];
                        @endphp
                        <div class="flex items-start space-x-3 animate-fade-in">
                            <div class="w-3 h-3 rounded-full {{ $typeColors[$activity->type] ?? 'bg-muted-foreground' }} mt-2 flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start gap-2">
                                    <p class="text-sm font-medium text-foreground truncate">
                                        {{ $activity->action }} • {{ $activity->description }}
                                    </p>
                                    <span class="text-xs text-muted-foreground whitespace-nowrap">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-xs text-muted-foreground mt-1">Par {{ $activity->user->name ?? 'Système' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12">
                            <i class="fas fa-inbox text-4xl text-muted-foreground mb-3 opacity-50"></i>
                            <p class="text-sm text-muted-foreground">Aucune activité enregistrée.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-card/50 backdrop-blur-sm rounded-xl p-6 border border-border hover-glow-purple">
            <h3 class="text-lg font-semibold text-foreground mb-4 text-glow-purple">Actions rapides</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{route('admin.pages')}}">
                    <div class="p-4 bg-primary/5 rounded-lg border border-primary/20 hover-lift hover-glow-purple cursor-pointer transition-all">
                        <h4 class="font-medium text-foreground">Créer une page</h4>
                        <p class="text-sm text-muted-foreground mt-1">Ajouter une nouvelle page à votre site</p>
                    </div>
                </a>
                <a href="{{route('admin.articles')}}">
                    <div class="p-4 bg-success/5 rounded-lg border border-success/20 hover-lift hover-glow-purple cursor-pointer transition-all">
                        <h4 class="font-medium text-foreground">Nouvel article</h4>
                        <p class="text-sm text-muted-foreground mt-1">Rédiger et publier un article</p>
                    </div>
                </a>
                <a href="{{route('modules.index')}}">
                    <div class="p-4 bg-purple-500/5 rounded-lg border border-purple-500/20 hover-lift hover-glow-purple cursor-pointer transition-all">
                        <h4 class="font-medium text-foreground">Installer un module</h4>
                        <p class="text-sm text-muted-foreground mt-1">Étendre les fonctionnalités</p>
                    </div>
                </a>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        .active-range-btn {
            background-color: hsl(var(--primary)) !important;
            color: hsl(var(--primary-foreground)) !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: hsl(var(--muted));
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: hsl(var(--muted-foreground));
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: hsl(var(--foreground));
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/echarts@6.0.0/dist/echarts.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartDom = document.getElementById('visitorsChart');
            if (!chartDom) return;

            const myChart = echarts.init(chartDom);
            const rangeButtons = document.querySelectorAll('[data-range]');
            const totalVisitorsSpan = document.getElementById('totalVisitors');
            const avgVisitorsSpan = document.getElementById('avgVisitors');

            function hslToRgb(h, s, l) {
                s /= 100;
                l /= 100;
                const k = n => (n + h / 30) % 12;
                const a = s * Math.min(l, 1 - l);
                const f = n => l - a * Math.max(-1, Math.min(k(n) - 3, Math.min(9 - k(n), 1)));
                return [
                    Math.round(255 * f(0)),
                    Math.round(255 * f(8)),
                    Math.round(255 * f(4))
                ];
            }

            function getThemeColors() {
                const rootStyles = getComputedStyle(document.documentElement);

                function parseHSL(hslString) {
                    const values = hslString.trim().split(/\s+/).map(v => parseFloat(v));
                    if (values.length >= 3) {
                        const [r, g, b] = hslToRgb(values[0], values[1], values[2]);
                        return { r, g, b };
                    }
                    return { r: 147, g: 51, b: 234 };
                }

                const primary = parseHSL(rootStyles.getPropertyValue('--primary'));
                const foreground = parseHSL(rootStyles.getPropertyValue('--foreground'));
                const mutedForeground = parseHSL(rootStyles.getPropertyValue('--muted-foreground'));
                const border = parseHSL(rootStyles.getPropertyValue('--border'));

                return {
                    primary: `rgb(${primary.r}, ${primary.g}, ${primary.b})`,
                    primaryRgba: (alpha) => `rgba(${primary.r}, ${primary.g}, ${primary.b}, ${alpha})`,
                    foreground: `rgb(${foreground.r}, ${foreground.g}, ${foreground.b})`,
                    mutedForeground: `rgb(${mutedForeground.r}, ${mutedForeground.g}, ${mutedForeground.b})`,
                    border: `rgb(${border.r}, ${border.g}, ${border.b})`,
                };
            }

            const themeColors = getThemeColors();

            function loadChartData(days = 7) {
                myChart.showLoading({
                    text: 'Chargement...',
                    color: themeColors.primary,
                    textColor: themeColors.foreground,
                    maskColor: 'rgba(0, 0, 0, 0.3)'
                });

                fetch(`/admin/visitors/data/${days}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Erreur réseau');
                        return response.json();
                    })
                    .then(result => {
                        myChart.hideLoading();

                        totalVisitorsSpan.textContent = result.total.toLocaleString('fr-FR');
                        avgVisitorsSpan.textContent = result.average;

                        const option = {
                            tooltip: {
                                trigger: 'axis',
                                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                                borderColor: themeColors.border,
                                borderWidth: 1,
                                textStyle: {
                                    color: '#fff',
                                    fontSize: 13
                                },
                                padding: [12, 16],
                                axisPointer: {
                                    type: 'cross',
                                    crossStyle: {
                                        color: themeColors.mutedForeground,
                                        opacity: 0.5
                                    },
                                    lineStyle: {
                                        color: themeColors.primary,
                                        width: 1,
                                        type: 'dashed'
                                    }
                                },
                                formatter: function(params) {
                                    if (!params || params.length === 0) return '';
                                    const point = params[0];
                                    return `
                                <div style="font-weight: 600; margin-bottom: 8px; font-size: 14px;">${point.name}</div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="display:inline-block;width:10px;height:10px;background-color:${point.color};border-radius:50%;"></span>
                                    <span style="color: rgba(255,255,255,0.8);">Visiteurs:</span>
                                    <strong style="margin-left: auto; font-size: 15px;">${point.value}</strong>
                                </div>
                            `;
                                }
                            },
                            grid: {
                                left: '2%',
                                right: '2%',
                                bottom: '10%',
                                top: '5%',
                                containLabel: true
                            },
                            xAxis: {
                                type: 'category',
                                boundaryGap: false,
                                data: result.labels,
                                axisLine: {
                                    lineStyle: {
                                        color: themeColors.border,
                                        width: 1
                                    }
                                },
                                axisTick: {
                                    show: false
                                },
                                axisLabel: {
                                    color: themeColors.mutedForeground,
                                    fontSize: 11,
                                    margin: 12,
                                    rotate: days > 30 ? 45 : 0
                                }
                            },
                            yAxis: {
                                type: 'value',
                                minInterval: 1,
                                splitLine: {
                                    lineStyle: {
                                        color: themeColors.border,
                                        type: 'dashed',
                                        opacity: 0.4
                                    }
                                },
                                axisLine: {
                                    show: false
                                },
                                axisTick: {
                                    show: false
                                },
                                axisLabel: {
                                    color: themeColors.mutedForeground,
                                    fontSize: 11
                                }
                            },
                            series: [
                                {
                                    name: 'Visiteurs',
                                    type: 'line',
                                    smooth: 0.3,
                                    symbol: 'circle',
                                    symbolSize: 8,
                                    showSymbol: true,
                                    itemStyle: {
                                        color: themeColors.primary,
                                        borderWidth: 2,
                                        borderColor: '#fff'
                                    },
                                    lineStyle: {
                                        width: 3,
                                        color: themeColors.primary,
                                        shadowColor: themeColors.primaryRgba(0.4),
                                        shadowBlur: 8,
                                        shadowOffsetY: 2
                                    },
                                    areaStyle: {
                                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                            {
                                                offset: 0,
                                                color: themeColors.primaryRgba(0.35)
                                            },
                                            {
                                                offset: 1,
                                                color: themeColors.primaryRgba(0.02)
                                            }
                                        ])
                                    },
                                    emphasis: {
                                        focus: 'series',
                                        itemStyle: {
                                            color: themeColors.primary,
                                            borderWidth: 3,
                                            borderColor: '#fff',
                                            shadowBlur: 12,
                                            shadowColor: themeColors.primaryRgba(0.6)
                                        },
                                        scale: 1.1
                                    },
                                    data: result.data
                                }
                            ],
                            animation: true,
                            animationDuration: 750,
                            animationEasing: 'cubicOut'
                        };

                        myChart.setOption(option, true);
                    })
                    .catch(error => {
                        myChart.hideLoading();
                        console.error('Erreur lors du chargement des données:', error);

                        myChart.setOption({
                            title: {
                                text: 'Erreur de chargement des données',
                                subtext: 'Veuillez réessayer',
                                left: 'center',
                                top: 'center',
                                textStyle: {
                                    color: themeColors.mutedForeground,
                                    fontSize: 14,
                                    fontWeight: 'normal'
                                },
                                subtextStyle: {
                                    color: themeColors.mutedForeground,
                                    fontSize: 12
                                }
                            }
                        });
                    });
            }

            rangeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const days = parseInt(this.getAttribute('data-range'));

                    rangeButtons.forEach(btn => {
                        btn.classList.remove('bg-primary', 'text-primary-foreground', 'active-range-btn');
                        btn.classList.add('text-muted-foreground');
                    });

                    this.classList.remove('text-muted-foreground');
                    this.classList.add('bg-primary', 'text-primary-foreground', 'active-range-btn');

                    loadChartData(days);
                });
            });

            loadChartData(7);

            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (myChart) {
                        myChart.resize();
                    }
                }, 250);
            });

            window.addEventListener('beforeunload', function() {
                if (myChart) {
                    myChart.dispose();
                }
            });
        });
    </script>
@endpush
