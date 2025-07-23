// Canvas setup
const canvas = document.getElementById('drawingCanvas');
const ctx = canvas.getContext('2d');
const colorPicker = document.getElementById('colorPicker');
const brushSize = document.getElementById('brushSize');
const brushSizeValue = document.getElementById('brushSizeValue');

// Drawing state
let isDrawing = false;
let currentPath = [];
let allPaths = [];
let currentPhase = 1;
let phaseData = [];
let startTime = Date.now();
let strokeCount = 0;
let colorsUsed = new Set(['#000000']);

// Phase configuration
const phases = [
    {
        title: "Phase 1: Draw a House",
        description: "Draw a house as you imagine it. Include details like windows, doors, and surroundings. Let your intuition guide you.",
        icon: "🏠",
        analysis: "Represents your sense of home, family dynamics, and security"
    },
    {
        title: "Phase 2: Draw a Tree", 
        description: "Draw a tree with roots, trunk, and branches. Show it as you envision a tree should look.",
        icon: "🌳",
        analysis: "Reflects your life energy, personal growth, and inner strength"
    },
    {
        title: "Phase 3: Draw a Person",
        description: "Draw a person of any age or gender. Include as much detail as feels natural to you.",
        icon: "👤", 
        analysis: "Shows your self-image and how you relate to others"
    }
];

// Initialize stars background
function createStars() {
    const starsContainer = document.getElementById('stars');
    for (let i = 0; i < 100; i++) {
        const star = document.createElement('div');
        star.className = 'star';
        star.style.left = Math.random() * 100 + '%';
        star.style.top = Math.random() * 100 + '%';
        star.style.animationDelay = Math.random() * 3 + 's';
        starsContainer.appendChild(star);
    }
}

// Canvas event listeners
canvas.addEventListener('mousedown', startDrawing);
canvas.addEventListener('mousemove', draw);
canvas.addEventListener('mouseup', stopDrawing);
canvas.addEventListener('mouseout', stopDrawing);

// Touch events for mobile
canvas.addEventListener('touchstart', handleTouch);
canvas.addEventListener('touchmove', handleTouch);
canvas.addEventListener('touchend', stopDrawing);

function handleTouch(e) {
    e.preventDefault();
    const touch = e.touches[0];
    const rect = canvas.getBoundingClientRect();
    const x = touch.clientX - rect.left;
    const y = touch.clientY - rect.top;
    
    if (e.type === 'touchstart') {
        startDrawing({offsetX: x, offsetY: y});
    } else if (e.type === 'touchmove') {
        draw({offsetX: x, offsetY: y});
    }
}

function startDrawing(e) {
    isDrawing = true;
    currentPath = [{
        x: e.offsetX,
        y: e.offsetY,
        color: colorPicker.value,
        size: brushSize.value
    }];
    strokeCount++;
    updateInsights();
}

function draw(e) {
    if (!isDrawing) return;

    const point = {
        x: e.offsetX,
        y: e.offsetY,
        color: colorPicker.value,
        size: brushSize.value
    };

    currentPath.push(point);
    colorsUsed.add(colorPicker.value);

    // Draw the current stroke
    ctx.globalCompositeOperation = 'source-over';
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = colorPicker.value;
    ctx.lineWidth = brushSize.value;

    if (currentPath.length > 1) {
        const prevPoint = currentPath[currentPath.length - 2];
        ctx.beginPath();
        ctx.moveTo(prevPoint.x, prevPoint.y);
        ctx.lineTo(point.x, point.y);
        ctx.stroke();
    }

    updateInsights();
}

function stopDrawing() {
    if (!isDrawing) return;
    isDrawing = false;
    if (currentPath.length > 0) {
        allPaths.push([...currentPath]);
        currentPath = [];
    }
}

function clearCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    allPaths = [];
    strokeCount = 0;
    colorsUsed = new Set([colorPicker.value]);
    updateInsights();
}

function undoLast() {
    if (allPaths.length > 0) {
        allPaths.pop();
        redrawCanvas();
        strokeCount = Math.max(0, strokeCount - 1);
        updateInsights();
    }
}

function redrawCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    allPaths.forEach(path => {
        if (path.length > 1) {
            ctx.strokeStyle = path[0].color;
            ctx.lineWidth = path[0].size;
            ctx.beginPath();
            ctx.moveTo(path[0].x, path[0].y);
            for (let i = 1; i < path.length; i++) {
                ctx.lineTo(path[i].x, path[i].y);
            }
            ctx.stroke();
        }
    });
}

function updateInsights() {
    const elapsed = Math.floor((Date.now() - startTime) / 1000);
    const minutes = Math.floor(elapsed / 60);
    const seconds = elapsed % 60;
    
    document.getElementById('drawingTime').textContent = 
        `${minutes}:${seconds.toString().padStart(2, '0')}`;
    document.getElementById('strokeCount').textContent = strokeCount;
    document.getElementById('colorCount').textContent = colorsUsed.size;
    
    // Calculate coverage (simplified)
    const coverage = Math.min(100, (strokeCount * 2));
    document.getElementById('coverage').textContent = coverage + '%';
}

function nextPhase() {
    // Save current phase data
    phaseData.push({
        phase: currentPhase,
        paths: [...allPaths],
        timeSpent: Date.now() - startTime,
        strokeCount: strokeCount,
        colorsUsed: Array.from(colorsUsed),
        coverage: Math.min(100, strokeCount * 2)
    });

    currentPhase++;
    
    if (currentPhase <= 3) {
        // Update UI for next phase
        const phase = phases[currentPhase - 1];
        document.getElementById('phaseTitle').textContent = phase.title;
        document.getElementById('phaseDescription').textContent = phase.description;
        document.getElementById('progressFill').style.width = (currentPhase / 3 * 100) + '%';
        
        // Update button text
        const btn = document.querySelector('.next-phase-btn');
        if (currentPhase === 3) {
            btn.textContent = 'Complete Analysis';
        } else {
            btn.textContent = 'Continue to Person';
        }
        
        // Clear canvas for next phase
        clearCanvas();
        startTime = Date.now();
        
    } else {
        // Show analysis
        showAnalysis();
    }
}

function showAnalysis() {
    document.querySelector('.main-content').style.display = 'none';
    document.getElementById('analysisPanel').classList.remove('hidden');
    
    const analysisGrid = document.getElementById('analysisGrid');
    analysisGrid.innerHTML = '';
    
    // Generate analysis based on drawing data
    phases.forEach((phase, index) => {
        const data = phaseData[index];
        const card = document.createElement('div');
        card.className = 'analysis-card';
        
        let insights = generateInsights(phase, data);
        
        card.innerHTML = `
            <div class="card-icon">${phase.icon}</div>
            <div class="card-title">${phase.title.split(':')[1]}</div>
            <p style="margin-bottom: 15px; opacity: 0.9;">${phase.analysis}</p>
            <div style="background: rgba(0,0,0,0.1); padding: 15px; border-radius: 10px;">
                ${insights}
            </div>
        `;
        
        analysisGrid.appendChild(card);
    });
    
    // Add overall analysis
    const overallCard = document.createElement('div');
    overallCard.className = 'analysis-card';
    overallCard.style.gridColumn = '1 / -1';
    
    overallCard.innerHTML = `
        <div class="card-icon">🔮</div>
        <div class="card-title">Overall Psychological Profile</div>
        <div style="background: rgba(0,0,0,0.1); padding: 20px; border-radius: 10px;">
            ${generateOverallAnalysis()}
        </div>
    `;
    
    analysisGrid.appendChild(overallCard);
}

function generateInsights(phase, data) {
    if (!data) return '<p>No data available for this phase.</p>';
    
    let insights = [];
    
    // Time analysis
    const minutes = Math.floor(data.timeSpent / 60000);
    if (minutes < 2) {
        insights.push(`<strong>Quick Drawing (${minutes}m):</strong> May indicate confidence or impulsiveness`);
    } else if (minutes > 5) {
        insights.push(`<strong>Detailed Approach (${minutes}m):</strong> Shows attention to detail and perfectionism`);
    }
    
    // Stroke analysis
    if (data.strokeCount < 20) {
        insights.push(`<strong>Minimalist Style:</strong> Prefers simplicity and essential elements`);
    } else if (data.strokeCount > 100) {
        insights.push(`<strong>Detail-Oriented:</strong> High investment in complexity and nuance`);
    }
    
    // Color analysis
    if (data.colorsUsed.length === 1) {
        insights.push(`<strong>Monochromatic:</strong> Focused, possibly conservative approach`);
    } else if (data.colorsUsed.length > 3) {
        insights.push(`<strong>Colorful Expression:</strong> Creative, emotionally expressive nature`);
    }
    
    return insights.length > 0 ? insights.map(i => `<p style="margin-bottom: 10px;">${i}</p>`).join('') 
        : '<p>Your drawing shows a balanced and thoughtful approach.</p>';
}

function generateOverallAnalysis() {
    const totalTime = phaseData.reduce((sum, data) => sum + data.timeSpent, 0);
    const totalStrokes = phaseData.reduce((sum, data) => sum + data.strokeCount, 0);
    const allColors = new Set();
    phaseData.forEach(data => data.colorsUsed.forEach(color => allColors.add(color)));
    
    let analysis = `
        <p style="margin-bottom: 15px;"><strong>Drawing Sequence Analysis:</strong> You completed the test in ${Math.floor(totalTime / 60000)} minutes with ${totalStrokes} total strokes using ${allColors.size} different colors.</p>
    `;
    
    // Determine drawing priority
    const phaseTimes = phaseData.map(d => d.timeSpent);
    const maxTimeIndex = phaseTimes.indexOf(Math.max(...phaseTimes));
    const priorities = ['Home & Security', 'Personal Growth', 'Self-Image'];
    
    analysis += `<p style="margin-bottom: 15px;"><strong>Primary Focus:</strong> ${priorities[maxTimeIndex]} - You invested the most time and energy in this area, suggesting it's currently significant in your psychological landscape.</p>`;
    
    // Artistic approach
    const avgStrokes = totalStrokes / 3;
    if (avgStrokes < 30) {
        analysis += `<p><strong>Artistic Approach:</strong> Minimalist and efficient - you focus on essential elements and avoid unnecessary complexity.</p>`;
    } else if (avgStrokes > 80) {
        analysis += `<p><strong>Artistic Approach:</strong> Highly detailed and expressive - you have a rich inner world and attention to nuance.</p>`;
    } else {
        analysis += `<p><strong>Artistic Approach:</strong> Balanced and thoughtful - you strike a good balance between simplicity and detail.</p>`;
    }
    
    return analysis;
}

// Brush size display update
brushSize.addEventListener('input', () => {
    brushSizeValue.textContent = brushSize.value + 'px';
});

// Initialize
createStars();
updateInsights();

// Timer update
setInterval(updateInsights, 1000);
