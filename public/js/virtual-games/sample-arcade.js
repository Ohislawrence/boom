window.initVirtualGame = function(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !canvas.getContext) {
        throw new Error('Canvas not found or unsupported.');
    }

    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;

    const player = {
        x: width / 2 - 24,
        y: height - 100,
        size: 48,
        speed: 6,
        color: '#42b883',
    };

    const stars = [];
    const obstacles = [];
    let score = 0;
    let lives = 3;
    let gameOver = false;
    let keys = {};
    let tick = 0;

    function addStar() {
        stars.push({
            x: Math.random() * (width - 30) + 15,
            y: -20,
            radius: 14,
            speed: 2.5 + Math.random() * 1.8,
            color: '#f8e71c',
        });
    }

    function addObstacle() {
        obstacles.push({
            x: Math.random() * (width - 60) + 10,
            y: -40,
            width: 60,
            height: 24,
            speed: 3 + Math.random() * 2.2,
            color: '#ff5c5c',
        });
    }

    function drawPlayer() {
        ctx.fillStyle = player.color;
        ctx.fillRect(player.x, player.y, player.size, player.size);
        ctx.strokeStyle = '#ffffff';
        ctx.lineWidth = 3;
        ctx.strokeRect(player.x, player.y, player.size, player.size);
    }

    function drawStar(star) {
        ctx.beginPath();
        ctx.fillStyle = star.color;
        ctx.arc(star.x, star.y, star.radius, 0, Math.PI * 2);
        ctx.fill();
        ctx.closePath();
    }

    function drawObstacle(obstacle) {
        ctx.fillStyle = obstacle.color;
        ctx.fillRect(obstacle.x, obstacle.y, obstacle.width, obstacle.height);
    }

    function drawOverlay() {
        ctx.fillStyle = 'rgba(0, 0, 0, 0.55)';
        ctx.fillRect(0, 0, width, 60);

        ctx.fillStyle = '#ffffff';
        ctx.font = '22px Inter, sans-serif';
        ctx.fillText(`Score: ${score}`, 18, 36);
        ctx.fillText(`Lives: ${lives}`, width - 140, 36);

        if (gameOver) {
            ctx.fillStyle = 'rgba(0, 0, 0, 0.75)';
            ctx.fillRect(0, 0, width, height);
            ctx.fillStyle = '#ffffff';
            ctx.font = '48px Inter, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('Game Over', width / 2, height / 2 - 20);
            ctx.font = '24px Inter, sans-serif';
            ctx.fillText('Refresh to play again', width / 2, height / 2 + 24);
        }
    }

    function clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    function rectsIntersect(a, b) {
        return a.x < b.x + b.width && a.x + a.width > b.x && a.y < b.y + b.height && a.y + a.height > b.y;
    }

    function update() {
        if (gameOver) {
            return;
        }

        tick += 1;
        if (tick % 90 === 0) {
            addStar();
        }
        if (tick % 120 === 0) {
            addObstacle();
        }

        if (keys.ArrowLeft || keys.KeyA) {
            player.x -= player.speed;
        }
        if (keys.ArrowRight || keys.KeyD) {
            player.x += player.speed;
        }
        if (keys.ArrowUp || keys.KeyW) {
            player.y -= player.speed;
        }
        if (keys.ArrowDown || keys.KeyS) {
            player.y += player.speed;
        }

        player.x = clamp(player.x, 0, width - player.size);
        player.y = clamp(player.y, 60, height - player.size);

        stars.forEach((star, index) => {
            star.y += star.speed;
            const playerRect = { x: player.x, y: player.y, width: player.size, height: player.size };
            const starRect = { x: star.x - star.radius, y: star.y - star.radius, width: star.radius * 2, height: star.radius * 2 };
            if (rectsIntersect(playerRect, starRect)) {
                stars.splice(index, 1);
                score += 10;
            } else if (star.y - star.radius > height) {
                stars.splice(index, 1);
            }
        });

        obstacles.forEach((obstacle, index) => {
            obstacle.y += obstacle.speed;
            const playerRect = { x: player.x, y: player.y, width: player.size, height: player.size };
            const obstacleRect = { x: obstacle.x, y: obstacle.y, width: obstacle.width, height: obstacle.height };
            if (rectsIntersect(playerRect, obstacleRect)) {
                obstacles.splice(index, 1);
                lives -= 1;
                if (lives <= 0) {
                    gameOver = true;
                }
            } else if (obstacle.y > height) {
                obstacles.splice(index, 1);
            }
        });
    }

    function draw() {
        ctx.clearRect(0, 0, width, height);

        ctx.fillStyle = '#090b14';
        ctx.fillRect(0, 0, width, height);

        stars.forEach(drawStar);
        obstacles.forEach(drawObstacle);
        drawPlayer();
        drawOverlay();
    }

    function loop() {
        update();
        draw();
        window.requestAnimationFrame(loop);
    }

    document.addEventListener('keydown', (event) => {
        keys[event.code] = true;
    });

    document.addEventListener('keyup', (event) => {
        keys[event.code] = false;
    });

    loop();
};
