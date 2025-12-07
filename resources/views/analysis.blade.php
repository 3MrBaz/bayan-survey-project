<x-layout>
    <section class="bg-[#262626] min-h-screen pb-32 pt-12 lg:pt-0">

        <!-- Top Yellow Shape -->
        <div>
            <svg viewBox="0 0 1925 375" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0021713 0L0.000976562 375.003L1925 0H0.0021713Z"
                    fill="#F9B800"></path>
            </svg>
        </div>

        <h1 class="text-center text-4xl text-white mt-[-40px] mb-10 font-bold">
            تحليل نتائج الاستبيان
        </h1>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- =======================
            CHARTS SECTION (5 charts)
        ======================== -->
        <div class="w-[90%] md:w-[70%] mx-auto space-y-10">

            <!-- 1) Score Distribution -->
            <div class="bg-white p-6 rounded-lg shadow border border-amber-300">
                <h2 class="text-2xl font-bold text-amber-500 mb-4 text-center">
                    توزيع الدرجات
                </h2>
                <canvas id="scoresChart"></canvas>
            </div>

            <!-- 2) Completion Over Time -->
            <div class="bg-white p-6 rounded-lg shadow border border-amber-300">
                <h2 class="text-2xl font-bold text-amber-500 mb-4 text-center">
                    عدد المشاركات عبر الزمن
                </h2>
                <canvas id="completionChart"></canvas>
            </div>

            <!-- 3) Time vs Score (scatter) -->
            <div class="bg-white p-6 rounded-lg shadow border border-amber-300">
                <h2 class="text-2xl font-bold text-amber-500 mb-4 text-center">
                    العلاقة بين الوقت والدرجة
                </h2>
                <canvas id="timeScoreChart"></canvas>
            </div>

            <!-- 4) Most Missed Questions -->
            <div class="bg-white p-6 rounded-lg shadow border border-amber-300">
                <h2 class="text-2xl font-bold text-amber-500 mb-4 text-center">
                    الأسئلة الأكثر أخطاءً
                </h2>
                <canvas id="missedChart"></canvas>
            </div>

            <!-- 5) Answer Distribution per Question (dropdown) -->
            <div class="bg-white p-6 rounded-lg shadow border border-amber-300">
                <h2 class="text-2xl font-bold text-amber-500 mb-4 text-center">
                    توزيع الإجابات حسب السؤال
                </h2>

                <div class="flex justify-center mb-4">
                    <select id="questionSelect" class="select select-bordered w-full max-w-md text-[#262626]">
                        <option value="" class="text-center">اختر سؤالاً</option>
                        @foreach ($questionStats as $qs)
                            <option class="text-center" value="{{ $qs['id'] }}">
                                {{ mb_strlen($qs['question']) > 60 ? mb_substr($qs['question'], 0, 57) . '...' : $qs['question'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <canvas id="questionChart"></canvas>
            </div>

        </div>

        <!-- =======================
            ANSWERS TABLE
        ======================== -->
        <div
            class="w-[95%] md:w-[70%] mx-auto mt-16 bg-[#ffffe4] border border-amber-300 rounded-lg shadow py-6 overflow-x-auto">

            <h2 class="text-2xl font-semibold text-amber-500 mb-6 text-center">
                تفاصيل إجابات المستخدمين
            </h2>
            <table class="w-[90%] border border-amber-300 rounded-lg overflow-hidden mx-auto text-center">
                <thead class="bg-amber-400 text-[#262626] font-bold">
                    <tr>
                        <th class="p-3">المستخدم</th>
                        <th class="p-3">السؤال</th>
                        <th class="p-3">إجابة المستخدم</th>
                        <th class="p-3">التصحيح اليدوي</th>
                        <th class="p-3">حفظ</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($answers as $a)
                        {{-- Only show text-input questions --}}
                        @if ($a->question->type !== 'text')
                            @continue
                        @endif

                        <tr class="border-b border-amber-200 bg-white hover:bg-amber-50 transition">

                            <td class="p-3 text-[#262626]">
                                {{ $a->user->name ?? 'غير معروف' }}
                            </td>

                            <td class="p-3 text-[#262626]">
                                {{ $a->question->question }}
                            </td>

                            <td class="p-3 text-[#262626]">
                                {{ $a->answer }}
                            </td>

                            <td class="p-3 text-[#262626]">
                                <form action="{{ route('manual-grade') }}" method="POST"
                                    class="flex items-center space-x-3">
                                    @csrf
                                    <input type="hidden" name="answer_id" value="{{ $a->id }}">

                                    {{-- Corrected text answer --}}
                                    <input type="hidden" name="corrected_answer" value="{{ $a->answer }}">

                                    {{-- Mark correct/wrong --}}
                                    <select name="is_correct"
                                        class="select select-bordered text-[#262626] font-semibold">
                                        <option value="1" {{ $a->meta['is_correct'] ?? false ? 'selected' : '' }}>✔
                                            صحيح</option>
                                        <option value="0" {{ $a->meta['is_correct'] ?? true ? '' : 'selected' }}>✘
                                            خطأ</option>
                                    </select>

                            <td class="p-3">
                                <button type="submit"
                                    class="btn bg-amber-400 w-full text-white hover:bg-amber-400 hover:text-[#262626]">
                                    حفظ
                                </button>
                                </form>
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>


        </div>

        <!-- Bottom Yellow Shape -->
        <div class="rotate-180 mt-20">
            <svg viewBox="0 0 1925 375" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0021713 0L0.000976562 375.003L1925 0H0.0021713Z"
                    fill="#F9B800"></path>
            </svg>
        </div>

    </section>

    <!-- =======================
           CHART.JS SCRIPTS
    ======================== -->
    <script>
        // --------- SAFE BACKEND DATA EXTRACTION ---------
        let rawScores = @json($scores ?? []);
        let rawTimes = @json($times ?? []);
        let completionLabels = @json($completionLabels ?? []);
        let completionData = @json($completionData ?? []);
        let mostMissedLabels = @json($mostMissedLabels ?? []);
        let mostMissedData = @json($mostMissedData ?? []);
        let questionStats = @json($questionStats ?? []);

        // Normalize in case something came as an object instead of array
        if (!Array.isArray(rawScores)) rawScores = Object.values(rawScores);
        if (!Array.isArray(rawTimes)) rawTimes = Object.values(rawTimes);
        if (!Array.isArray(completionLabels)) completionLabels = Object.values(completionLabels);
        if (!Array.isArray(completionData)) completionData = Object.values(completionData);
        if (!Array.isArray(mostMissedLabels)) mostMissedLabels = Object.values(mostMissedLabels);
        if (!Array.isArray(mostMissedData)) mostMissedData = Object.values(mostMissedData);
        if (!Array.isArray(questionStats)) questionStats = Object.values(questionStats);

        const defaultChartStyle = {
            borderWidth: 2,
            backgroundColor: "rgba(249,184,0,0.35)",
            borderColor: "#F9B800",
            tension: 0.3,
        };

        // Helper to safely create a chart only if canvas exists
        function safeChart(canvasId, config) {
            const el = document.getElementById(canvasId);
            if (!el) return null;
            return new Chart(el, config);
        }

        // ==============================
        // 1) SCORE DISTRIBUTION
        // ==============================
        (function() {
            if (!rawScores.length) return; // no data -> skip quietly

            const scoreCounts = {};
            rawScores.forEach((s) => {
                const key = (s ?? 0).toString();
                scoreCounts[key] = (scoreCounts[key] || 0) + 1;
            });

            const scoreLabels = Object.keys(scoreCounts);
            const scoreData = Object.values(scoreCounts);

            safeChart('scoresChart', {
                type: 'bar',
                data: {
                    labels: scoreLabels,
                    datasets: [{
                        label: 'عدد المستخدمين',
                        data: scoreData,
                        ...defaultChartStyle
                    }]
                },
                options: {
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'الدرجة'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'عدد المستخدمين'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        })();

        // ==============================
        // 2) COMPLETION OVER TIME
        // ==============================
        (function() {
            if (!completionLabels.length || !completionData.length) return;

            safeChart('completionChart', {
                type: 'line',
                data: {
                    labels: completionLabels,
                    datasets: [{
                        label: 'عدد المشاركات',
                        data: completionData,
                        ...defaultChartStyle
                    }]
                },
                options: {
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'التاريخ'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'عدد المشاركات'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        })();

        // ==============================
        // 3) TIME vs SCORE (SCATTER)
        // ==============================
        (function() {
            if (!rawScores.length || !rawTimes.length) return;

            const points = rawScores.map((score, idx) => ({
                x: Number(rawTimes[idx] ?? 0),
                y: Number(score ?? 0),
            }));

            safeChart('timeScoreChart', {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: 'الوقت مقابل الدرجة',
                        data: points,
                        ...defaultChartStyle
                    }]
                },
                options: {
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'الوقت (ثواني)'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'الدرجة'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        })();

        // ==============================
        // 4) MOST MISSED QUESTIONS
        // ==============================
        (function() {
            if (!mostMissedLabels.length || !mostMissedData.length) return;

            safeChart('missedChart', {
                type: 'bar',
                data: {
                    labels: mostMissedLabels,
                    datasets: [{
                        label: 'عدد الأخطاء',
                        data: mostMissedData,
                        ...defaultChartStyle
                    }]
                },
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        })();

        // ==============================
        // 5) PER-QUESTION DISTRIBUTION
        // ==============================
        (function() {
            const selectEl = document.getElementById('questionSelect');
            const canvas = document.getElementById('questionChart');
            if (!selectEl || !canvas) return;

            const ctx = canvas.getContext('2d');

            const qChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'عدد الإجابات',
                        data: [],
                        ...defaultChartStyle
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            selectEl.addEventListener('change', function() {
                const id = parseInt(this.value);
                const stat = questionStats.find(q => q.id === id);
                if (!stat) {
                    qChart.data.labels = [];
                    qChart.data.datasets[0].data = [];
                    qChart.update();
                    return;
                }

                qChart.data.labels = stat.labels || [];
                qChart.data.datasets[0].data = stat.data || [];
                qChart.update();
            });
        })();
    </script>


</x-layout>
