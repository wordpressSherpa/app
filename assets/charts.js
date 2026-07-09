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

        const {
            left,
            right,
            top,
            bottom
        } = chartArea;

        const xZero = scales.x.getPixelForValue(0);

        const yNinety = scales.y.getPixelForValue(90);
        const yEighty = scales.y.getPixelForValue(80);
        const ySeventy = scales.y.getPixelForValue(70);

        ctx.save();

        // Chart Color Zones
        // A Zone (90-100)
        ctx.fillStyle = 'rgba(22, 163, 74, 0.10)';
        ctx.fillRect(left, top, right - left, yNinety - top);

        // B Zone (80-89)
        ctx.fillStyle = 'rgba(22, 163, 74, 0.06)';
        ctx.fillRect(left, yNinety, right - left, yEighty - yNinety);

        // C Zone (70-79)
        ctx.fillStyle = 'rgba(245, 197, 66, 0.08)';
        ctx.fillRect(left, yEighty, right - left, ySeventy - yEighty);

        // D/F Zone (<70)
        ctx.fillStyle = 'rgba(255, 107, 107, 0.08)';
        ctx.fillRect(left, ySeventy, right - left, bottom - ySeventy);

        // Vertical center line at 0R
        ctx.strokeStyle = 'rgba(255,255,255,.18)';
        ctx.lineWidth = 2;

        ctx.beginPath();
        ctx.moveTo(xZero, top);
        ctx.lineTo(xZero, bottom);
        ctx.stroke();

        ctx.restore();
    }
};

const quadrantLabels = {
    id: 'quadrantLabels',


    afterDraw(chart) {

        const {
            ctx,
            chartArea
        } = chart;

        if (!chartArea) return;

        const {
            left,
            right,
            top,
            bottom
        } = chartArea;

        ctx.save();

        ctx.fillStyle = 'rgba(255,255,255,.75)';
        ctx.font = 'bold 14px Arial';
        ctx.textAlign = 'center';

        // Top Left
        ctx.fillText(
            '✅ Disciplined Red Day',
            left + ((right - left) * 0.30),
            top + 25
        );

        // Top Right
        ctx.fillText(
            '⚡ Disciplined Green Day',
            left + ((right - left) * 0.82),
            top + 25
        );

        // Bottom Left
        ctx.fillText(
            '🚨 Process Failure Red Day',
            left + ((right - left) * 0.32),
            bottom - 15
        );

        // Bottom Right
        ctx.fillText(
            '🍀 Lucky Green Day',
            left + ((right - left) * 0.82),
            bottom - 15
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

        plugins: [
            quadrantBackground,
            quadrantLabels
        ],


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

                            // Dot Color On Chart
                            if (point.y >= 90) {
                                return '#4ade80'; // A Grade
                            }

                            if (point.y >= 80) {
                                return '#16a34a'; // B Grade
                            }

                            if (point.y >= 70) {
                                return '#f5c542'; // C Grade
                            }

                            return '#ef4444'; // D/F Grade

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

                            if (r >= 0 && score >= 80) {
                                quadrant = '⚡ Disciplined Winner';
                            }
                            else if (r < 0 && score >= 80) {
                                quadrant = '✅ Good Loss';
                            }
                            else if (r >= 0 && score < 80) {
                                quadrant = '🍀 Lucky Win';
                            }
                            else {
                                quadrant = '🚨 Process Failure';
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