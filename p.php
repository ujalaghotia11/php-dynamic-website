<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vertical Scroll with Add and Remove</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #181818;
      font-family: Arial, sans-serif;
      color: #fff;
    }

    .container {
      margin: 50px auto;
      width: 360px;
      height: 400px; /* Fixed height to show 5 divs */
      overflow: hidden; /* Hide overflowing boxes */
      background-color: #2d2d2d;
      border-radius: 8px;
      padding: 10px;
      position: relative;
    }

    .box {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      height: 80px;
      background-color: #333;
      border-radius: 8px;
      margin-bottom: 10px;
      padding: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
      position: relative;
      transform: translateY(0); /* Default position for initial boxes */
      opacity: 1; /* Fully visible for initial boxes */
    }

    .box.animated {
      transform: translateY(-100%);
      opacity: 0;
      animation: slideIn 1s ease-out forwards;
    }

    .box.remove {
      animation: slideOut 1s ease-out forwards;
    }

    .box img {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
    }

    .box .content {
      flex-grow: 1;
      margin-left: 10px;
    }

    .box .content p {
      margin: 0;
      font-size: 14px;
    }

    .box .content .amount {
      color: #ffd700;
      font-size: 16px;
      font-weight: bold;
    }

    /* Slide In Animation */
    @keyframes slideIn {
      from {
        transform: translateY(-100%);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    /* Slide Out Animation */
    @keyframes slideOut {
      from {
        transform: translateY(0);
        opacity: 1;
      }
      to {
        transform: translateY(100%);
        opacity: 0;
      }
    }
  </style>
</head>
<body>
  <div class="container" id="box-container">
    <!-- Initial boxes will be dynamically added -->
    
  </div>

  <script>
    const container = document.getElementById('box-container');
    let count = 1; // Counter for new boxes

    // Function to create a new box
    function createBox(animated = true) {
      const box = document.createElement('div');
      box.className = 'box' + (animated ? ' animated' : '');
      box.innerHTML = `
        <img src="https://via.placeholder.com/50" alt="Profile">
        <div class="content">
          <p>Mem***${String.fromCharCode(65 + Math.floor(Math.random() * 26))}TK</p>
          <p class="amount">Receive ₹${(Math.random() * 2000 + 500).toFixed(2)}</p>
          <p>Winning amount</p>
        </div>
      `;
      return box;
    }

    // Function to add a new box at the top and remove the last box
    function addBox() {
      // Create a new box with animation
      const newBox = createBox(true);
      container.prepend(newBox);

      // Add animation to remove the last box if there are more than 5
      const existingBoxes = container.querySelectorAll('.box');
       // Apply a small transition to make boxes move smoothly
      
      if (existingBoxes.length > 5) {
        const lastBox = existingBoxes[existingBoxes.length - 1];
        lastBox.classList.add('remove'); // Add the slideOut animation
        setTimeout(() => lastBox.remove(), 1000); 
        // Remove the box after animation completes
      }
    }
   

    // Add the first 5 boxes without animations
    for (let i = 0; i < 5; i++) {
      const box = createBox(false); // Disable animation for initial load
      container.appendChild(box);
    }

    // Add a new box every 2 seconds after the initial load
    setInterval(addBox, 3000);
  </script>
</body>
</html>
