// ==========================
// Performance Curve Chart
// ==========================

const ctx = document.getElementById('performanceChart');

if (ctx && window.curveLabels) {

    new Chart(ctx, {

        type: 'line',

        data: {

            labels: window.curveLabels,

            datasets: [

                {
                    label: 'Daily Score',
                    data: window.curveScores,
                    borderColor: '#00e08a',
                    backgroundColor: 'rgba(0,224,138,.12)',
                    borderWidth: 3,
                    tension: 0.35,
                    pointRadius: 4,
                    fill: true
                },

                {
                    label: 'Process Trend',
                    data: window.curveAverage,
                    borderColor: '#ffffff',
                    borderWidth: 4,
                    tension: 0.35,
                    pointRadius: 0,
                    fill: false
                },

                {
                    label: 'A Grade Threshold',
                    data: Array(window.curveScores.length).fill(90),
                    borderColor: '#f5c542',
                    borderWidth: 2,
                    borderDash: [8, 6],
                    pointRadius: 0,
                    fill: false
                },

                {
                    label: 'B Grade Threshold',
                    data: Array(window.curveScores.length).fill(80),
                    borderColor: '#7d8590',
                    borderWidth: 2,
                    borderDash: [6, 6],
                    pointRadius: 0,
                    fill: false
                }

            ]

        },

        options: {

            responsive: true,

            plugins: {

                legend: {

                    display: true,

                    labels: {

                        color: '#ffffff',

                        font: {
                            size: 14,
                            weight: 'bold'
                        }

                    }

                }

            },

            scales: {

                y: {

                    min: 50,
                    max: 100,

                    ticks: {
                        color: '#9ca3af'
                    },

                    grid: {
                        color: 'rgba(255,255,255,.08)'
                    }

                },

                x: {

                    ticks: {
                        color: '#9ca3af'
                    },

                    grid: {
                        display: false
                    }

                }

            }

        }

    });

}


// ==========================
// Process Breakdown Radar
// ==========================

const radarCtx = document.getElementById('radarChart');

if (radarCtx && window.radarData) {

    new Chart(radarCtx, {

        type: 'radar',

        data: {

            labels: [
                `Rules (${window.radarData[0]}%)`,
                `Emotion (${window.radarData[1]}%)`,
                `Setup (${window.radarData[2]}%)`,
                `Context (${window.radarData[3]}%)`,
                `Execution (${window.radarData[4]}%)`
            ],

            datasets: [{

                label: 'Process Score',

                data: window.radarData,

                borderColor: '#00e08a',

                backgroundColor: 'rgba(0,224,138,.20)',

                borderWidth: 3

            }]

        },

        options: {

            responsive: true,

            scales: {

                r: {

                    min: 0,
                    max: 100,

                    ticks: {
                        display: false
                    },

                    grid: {
                        color: 'rgba(255,255,255,.08)'
                    },

                    angleLines: {
                        color: 'rgba(255,255,255,.08)'
                    },

                    pointLabels: {

                        color: '#ffffff',

                        font: {
                            size: 14,
                            weight: 'bold'
                        }

                    }

                }

            },

            plugins: {

                legend: {
                    display: false
                }

            }

        }

    });

}


// ==========================
// Quadrant Background Plugin
// ==========================

const quadrantBackground = {

    id: 'quadrantBackground',

    beforeDraw(chart) {

        const {
            ctx,
            chartArea,
            scales
        } = chart;

        if (!chartArea) return;

        const xZero = scales.x.getPixelForValue(0);
        const yNinety = scales.y.getPixelForValue(90);

        ctx.save();

        // --------------------
        // Background Colors
        // --------------------

        ctx.fillStyle = 'rgba(0,224,138,.08)';
        ctx.fillRect(
            xZero,
            chartArea.top,
            chartArea.right - xZero,
            yNinety - chartArea.top
        );

        ctx.fillStyle = 'rgba(59,130,246,.08)';
        ctx.fillRect(
            chartArea.left,
            chartArea.top,
            xZero - chartArea.left,
            yNinety - chartArea.top
        );

        ctx.fillStyle = 'rgba(245,197,66,.08)';
        ctx.fillRect(
            xZero,
            yNinety,
            chartArea.right - xZero,
            chartArea.bottom - yNinety
        );

        ctx.fillStyle = 'rgba(239,68,68,.08)';
        ctx.fillRect(
            chartArea.left,
            yNinety,
            xZero - chartArea.left,
            chartArea.bottom - yNinety
        );

        // --------------------
        // Quadrant Labels
        // --------------------

        ctx.fillStyle = 'rgba(255,255,255,.75)';
        ctx.font = 'bold 14px Arial';

        ctx.textAlign = 'center';

        // Top Right
        ctx.fillText(
            '⚡ Disciplined Green Day',
            xZero + ((chartArea.right - xZero) / 2),
            chartArea.top + 25
        );

        // Top Left
        ctx.fillText(
            '✅ Disciplined Red Day',
            chartArea.left + ((xZero - chartArea.left) / 2),
            chartArea.top + 25
        );

        // Bottom Right
        ctx.fillText(
            '🍀 Lucky Green Day',
            xZero + ((chartArea.right - xZero) / 2),
            chartArea.bottom - 15
        );

        // Bottom Left
        ctx.fillText(
            '🚨 Process Failure Red Day',
            chartArea.left + ((xZero - chartArea.left) / 2),
            chartArea.bottom - 15
        );

        ctx.restore();
    }

};


// ==========================
// Discipline vs Outcome Chart
// ==========================

const disciplineCtx =
    document.getElementById('disciplineChart');

if (
    disciplineCtx &&
    window.disciplineData
) {

    new Chart(disciplineCtx, {

        plugins: [quadrantBackground],

        type: 'scatter',

        data: {

            datasets: [

                {
                    label: 'Trading Days',

                    data: window.disciplineData,

                    pointRadius: 8,

                    pointHoverRadius: 10,

                    backgroundColor:
                        window.disciplineData.map(point => {

                            if (
                                point.x >= 0 &&
                                point.y >= 90
                            ) {
                                return '#00e08a';
                            }

                            if (
                                point.x < 0 &&
                                point.y >= 90
                            ) {
                                return '#3b82f6';
                            }

                            if (
                                point.x >= 0 &&
                                point.y < 90
                            ) {
                                return '#f5c542';
                            }

                            return '#ef4444';

                        })

                },

                {
                    type: 'line',

                    data: [
                        { x: 0, y: 60 },
                        { x: 0, y: 100 }
                    ],

                    borderColor: '#ffffff',

                    borderDash: [6, 6],

                    borderWidth: 2,

                    pointRadius: 0
                },

                {
                    type: 'line',

                    data: [
                        {
                            x: Math.min(
                                -2,
                                ...window.disciplineData.map(d => d.x)
                            ) - 0.5,
                            y: 90
                        },
                        {
                            x: Math.max(
                                2,
                                ...window.disciplineData.map(d => d.x)
                            ) + 0.5,
                            y: 90
                        }
                    ],
                }

            ]

        },

        options: {

            responsive: true,

            plugins: {

                legend: {
                    display: false
                },

                tooltip: {

                    callbacks: {

                        label: function (context) {

                            const r = context.raw.x;
                            const score = context.raw.y;

                            let quadrant = '';

                            if (r >= 0 && score >= 90) {
                                quadrant = '⚡ Disciplined Green Day';
                            }
                            else if (r < 0 && score >= 90) {
                                quadrant = '✅ Disciplined Red Day';
                            }
                            else if (r >= 0 && score < 90) {
                                quadrant = '🍀 Undisciplined Green Day';
                            }
                            else {
                                quadrant = '🚨 Undisciplined Red Day';
                            }

                            const tradeDate = context.raw.date;
                            const grade = context.raw.grade;

                            return [
                                tradeDate,
                                quadrant,
                                `Grade: ${grade} (${Math.round(score)}%)`,
                                `Net R: ${r.toFixed(1)}R`
                            ];

                        }

                    }

                }

            },

            scales: {

                // ==========================
                // Dynamic X-Axis Range
                // ==========================

                x: {

                    min: Math.min(
                        -2,
                        ...window.disciplineData.map(d => d.x)
                    ) - 0.5,

                    max: Math.max(
                        2,
                        ...window.disciplineData.map(d => d.x)
                    ) + 0.5,

                    title: {

                        display: true,

                        text: 'Net R'

                    },

                    grid: {
                        color: 'rgba(255,255,255,.08)'
                    },

                    ticks: {
                        color: '#9ca3af'
                    }

                },

                y: {

                    min: 60,
                    max: 100,

                    title: {

                        display: true,

                        text: 'Daily Score'

                    },

                    grid: {
                        color: 'rgba(255,255,255,.08)'
                    },

                    ticks: {
                        color: '#9ca3af'
                    }

                }

            }

        }

    });

}