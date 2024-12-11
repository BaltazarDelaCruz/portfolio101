    <?php
    include('../config/portfolioDB.php');
    session_start();
    if (isset($_SESSION['id']) && isset($_SESSION['username'])) {
        $user_id = $_SESSION['id'];
        $username = $_SESSION['username'];

        

        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :user_id');
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare('
            SELECT 
                users.username, 
                users.id AS portfolio_id,
                landingpage.*,
                users.public_id
            FROM users
            INNER JOIN landingpage ON users.id = landingpage.user_id 
            WHERE users.id = :user_id
        ');
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $portfolio = $stmt->fetch(PDO::FETCH_ASSOC);

    
        $stmt = $pdo->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_messages WHERE read_status = 0");
        $stmt->execute();
        $unreadCount = $stmt->fetchColumn();

    } else {
        echo "You are not logged in.";
        exit;
    }
    $age = '';
    if (isset($portfolio['birthdate']) && !empty($portfolio['birthdate'])) {
        $birthdate = DateTime::createFromFormat('Y-m-d', $portfolio['birthdate']); 
        $today = new DateTime();
        if ($birthdate) { 
            $age = $today->diff($birthdate)->y; 
        }
    }
    $stmt = $pdo->prepare('
        SELECT 
            skills.skill_name,
            skills.image_skills,
            skills.id AS skill_id
        FROM skills
        WHERE skills.user_id = :user_id
        ');
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | <?= isset($portfolio['navbrand']) ? htmlspecialchars($portfolio['navbrand']) :''?></title>
    <link rel="icon" href="<?= isset($portfolio['image_path']) ? htmlspecialchars($portfolio['image_path']) :'default.jpg'?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=arrow_drop_down" />
    <link href="style.css" rel="stylesheet">

    </head>

    <body>

    <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 10px; left: 50%; transform: translateX(-50%); z-index: 1050; width: 80%; text-align: center;">
        <?= htmlspecialchars($_SESSION['success_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
    <?php elseif (isset($_SESSION['warning_message'])): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert" style="position: fixed; top: 10px; left: 50%; transform: translateX(-50%); z-index: 1050; width: 80%; text-align: center;">
        <?= htmlspecialchars($_SESSION['warning_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['warning_message']); ?>
    <?php endif; ?>


    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
            <h2>Welcome to Portfolio, <?= htmlspecialchars($username) ?>!</h2>

            </ul>
            <li class="nav-item dropdown">
        <a href="#" class="dropdown-toggle" id="myAccountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            
            My Account<span class="material-symbols-outlined">arrow_drop_down</span>
        </a>
        <ul class="dropdown-menu" aria-labelledby="myAccountDropdown">
            <li>
                <p class="dropdown-item-text d-flex align-items-center">
                    <i class="fas fa-smile me-2"></i> Welcome, <?= htmlspecialchars($username) ?>!
                </p>
            </li>
            <li>
                <hr class="dropdown-divider custom-divider">
            </li>
            <li>
            <li>
        <a class="dropdown-item d-flex align-items-center" href="../client/home.php?portfolio=<?= htmlspecialchars($portfolio['public_id']) ?>" target="_blank">
            <i class="fas fa-eye me-2"></i> Preview my Account
        </a>
    </li>

    </li>


            <li>
                <a class="dropdown-item d-flex align-items-center" href="add.php?id=<?= isset($portfolio['portfolio_id']) ? htmlspecialchars($portfolio['portfolio_id']) : '' ?>">
                <i class="fas fa-edit me-2"></i> Edit
                </a>
            </li>
            <hr class="dropdown-divider custom-divider">
        
            <li>
    
        <h6 class="dropdown-header text-muted small">Exit</h6>

        <a class="dropdown-item d-flex align-items-center" href="logout.php">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </a>
    </li>

        </ul>
    </li>

        </div>
        <a href="#" class="nav-link" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-comment" data-bs-toggle="tooltip" title="New Messages" style="cursor: pointer;"></i>
        <span class="badge bg-danger" id="notificationCount"><?= $unreadCount > 0 ? $unreadCount : '' ?></span>
    </a>

    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" data-bs-auto-close="outside" style="max-height: 300px; overflow-y: auto; background-color:white; width:350px;">
        <li class="text-end px-2 py-1">
            <i id="toggleCheckboxes" class="fas fa-trash-alt text-danger" data-bs-toggle="tooltip" title="Select messages to delete" style="cursor: pointer;"></i>
        </li>
        <form id="deleteMessagesForm">
            <?php foreach ($messages as $message): ?>
                <li class="d-flex align-items-center">
                    <input type="checkbox" class="form-check-input me-2 d-none" name="message_ids[]" value="<?= $message['id'] ?>" style="border-radius:100%; margin-left: 10px; border-color:#000;">
                    <a class="dropdown-item text-dark w-100" href="#" style="font-size: smaller;"
                    data-bs-toggle="modal" 
                    data-bs-target="#messageModal" 
                    data-id="<?= $message['id'] ?>" 
                    data-name="<?= htmlspecialchars($message['name']) ?>" 
                    data-email="<?= htmlspecialchars($message['email']) ?>" 
                    data-message="<?= htmlspecialchars($message['message']) ?>" 
                    data-read-status="<?= $message['read_status'] ?>">
                        <strong><?= htmlspecialchars($message['name']) ?></strong>: 
                        <?= htmlspecialchars(substr($message['message'], 0, 30)) ?>...
                        <?php if ($message['read_status'] == 0): ?>
                            <span class="badge bg-danger">New</span>
                        <?php endif; ?>
                        <span class="text-muted" style="font-size: small;">
                            <?php
                                $currentTime = new DateTime();
                                $createdAt = new DateTime($message['created_at']);
                                $interval = $currentTime->diff($createdAt);
                                
                                if ($interval->y > 0) {
                                    echo $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
                                } elseif ($interval->m > 0) {
                                    echo $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
                                } elseif ($interval->d > 0) {
                                    echo $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
                                } elseif ($interval->h > 0) {
                                    echo $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
                                } elseif ($interval->i > 0) {
                                    echo $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
                                } else {
                                    echo $interval->s . ' second' . ($interval->s > 1 ? 's' : '') . ' ago';
                                }
                            ?>
                        </span>
                    </a>
                    </a>
                </li>
            <?php endforeach; ?>
        </form>
        <?php if (empty($messages)): ?>
            <li class="dropdown-item text-dark text-center" style="font-size:medium;">No new messages.</li>
        <?php endif; ?>
        <li class="dropdown-item text-center">
            <button type="button" class="btn btn-danger btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete Selected</button>
        </li>
    </ul>

        </div>
    </nav>

    <section  class="home container">
        
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel" style="color: black;">Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p style="color:black;"><strong>Name:</strong> <span id="modalName" class="text-dark"></span></p>
                    <p style="color:black;"></style><strong>Email:</strong> <span id="modalEmail" class="text-dark"></span></p>
                    <p style="color:black;"><strong>Message:</strong></p>
                    <p id="modalMessage" class="border p-2 bg-light text-dark"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel" style="color:black;">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="color:black;">
                    Are you sure you want to delete the selected messages?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel" style="color:black;">Notice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="color:black;">
                    Please select at least one message to delete.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>


    <script>
    document.addEventListener('click', function (event) {
        const target = event.target.closest('.dropdown-item');
        if (target) {
            const id = target.getAttribute('data-id'); 
            const name = target.getAttribute('data-name');
            const email = target.getAttribute('data-email');
            const message = target.getAttribute('data-message');
            const readStatus = target.getAttribute('data-read-status');

        
            document.getElementById('modalName').textContent = name;
            document.getElementById('modalEmail').textContent = email;
            document.getElementById('modalMessage').textContent = message;

        
            if (readStatus === '0') {
                fetch(`mark_read.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id }), 
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                        
                            const badge = document.getElementById('notificationCount');
                            let count = parseInt(badge.textContent) || 0;
                            if (count > 0) badge.textContent = count - 1;

                    
                            target.remove();

                        
                            const dropdown = document.querySelector('.dropdown-menu');
                            if (!dropdown.querySelector('.dropdown-item')) {
                                const noMessagesItem = document.createElement('li');
                                noMessagesItem.className = 'dropdown-item text-dark';
                            
                                dropdown.appendChild(noMessagesItem);
                            }
                        }
                    })
                    .catch(err => console.error('Error:', err));
            }
        }
    });

    
    document.getElementById('confirmDelete').addEventListener('click', function () {
        const form = document.getElementById('deleteMessagesForm');
        const selectedMessages = Array.from(form.querySelectorAll('input[name="message_ids[]"]:checked'));

        if (selectedMessages.length === 0) {
            const notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
            notificationModal.show();
            return;
        }

        const messageIds = selectedMessages.map(input => input.value);

        fetch('delete_messages.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message_ids: messageIds }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    selectedMessages.forEach(input => {
                        const listItem = input.closest('li');
                        listItem.remove();
                        alert('Messages deleted successfully!');
                        window.location.reload(); 
                    });

                    const remainingItems = form.querySelectorAll('li');
                    if (remainingItems.length === 0) {
                        const dropdown = document.querySelector('.dropdown-menu');
                        const noMessagesItem = document.createElement('li');
                        noMessagesItem.className = 'dropdown-item text-dark';
                    
                        dropdown.insertBefore(noMessagesItem, dropdown.lastElementChild);
                    }

                    const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    deleteModal.hide();
                } else {
                    console.error('Failed to delete messages.');
                }
            })
            .catch(err => {
                console.error('Error:', err);
            });
    });
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

        document.getElementById('toggleCheckboxes').addEventListener('click', function (event) {
        event.stopPropagation(); // Prevent the dropdown from closing when clicking the trashcan
        
        const checkboxes = document.querySelectorAll('input[name="message_ids[]"]');
        
        // Toggle the visibility of the checkboxes
        checkboxes.forEach(checkbox => {
            checkbox.classList.toggle('d-none'); // Toggle the 'd-none' class to show/hide checkboxes
        });
    });



    </script>

            <div class="text-center">
                
                <div class="d-flex align-items-center justify-content-center text-center">
                    <img src="<?= isset($portfolio['image_path']) ? htmlspecialchars($portfolio['image_path']) :'default.jpg'?>" alt="Your image" class="rounded-circle" style="height: 200px; width: 200px; border: solid #000; margin-right: 20px;">
                    <div>
                        <h1> Name <?= isset($portfolio['name']) ? htmlspecialchars($portfolio['name']) :''?>!</h1>
                        <h3><?= isset($portfolio['title']) ? htmlspecialchars($portfolio['title']) :'title'?></h3>
                    </div>
                </div>
                <h4>
                <?= isset($portfolio['body']) ? htmlspecialchars($portfolio['body']) :'Your body'?>
                </h4>
                <br>
                <a href="<?= isset($portfolio['button']) ? htmlspecialchars($portfolio['button']) :''?>" class="btn btn-black-primary">Hire me</a>
                
                <div class="text-center mt-5">
                <p class="text-center mb-5">Connect with me on social media!</p>
                <a style="color: white;" href="<?= isset($portfolio['fb']) ? htmlspecialchars($portfolio['fb']) : '' ?>" target="_blank" class="mx-2 social" title="Facebook">
                    <i class="fab fa-facebook fa-2x"></i>
                </a>
                <a style="color: white;" href="<?= isset($portfolio['github']) ? htmlspecialchars($portfolio['github']) : '' ?>" target="_blank" class="mx-2 social" title="GitHub">
                    <i class="fab fa-github fa-2x"></i>
                </a>
                <a style="color: white;" href="<?= isset($portfolio['telegram']) ? htmlspecialchars($portfolio['telegram']) : '' ?>" target="_blank" class="mx-2 social" title="Telegram">
                    <i class="fab fa-telegram fa-2x"></i>
                </a>
                <a style="color: white;" href="<?= isset($portfolio['linked']) ? htmlspecialchars($portfolio['linked']) : '' ?>" target="_blank" class="mx-2 social" title="LinkedIn">
                    <i class="fab fa-linkedin fa-2x"></i>
                </a>
                <a style="color: white;" href="mailto:<?= isset($portfolio['email']) ? htmlspecialchars($portfolio['email']) : '' ?>" class="mx-2 social" title="Email">
                    <i class="fas fa-envelope fa-2x"></i>
                </a>

    </div>
            </div>
    
    </section>
    <section class="aboutme container py-5">
        <div class="container">
        <h2 class="text-center mb-4">About Me</h2>
            <div class="d-flex align-items-center">
                <div class="about-content">
                    <p >
                        <?= isset($portfolio['aboutme']) ? htmlspecialchars($portfolio['aboutme']) : 'Write something about yourself' ?>
                    <br>
                    </p>
                    <br>
                    <div style="line-height: 1.8; font-size: 1rem;">
                        <p class="mb-3"><strong>College:</strong> <?= isset($portfolio['college']) ? htmlspecialchars($portfolio['college']) : 'N/A' ?></p>
                        <p class="mb-3"><strong>Degree:</strong> <?= isset($portfolio['degree']) ? htmlspecialchars($portfolio['degree']) : 'N/A' ?></p>
                        <p class="mb-3"><strong>Birthdate:</strong> <?= isset($portfolio['birthdate']) ? htmlspecialchars($portfolio['birthdate']) : 'N/A' ?></p>
                        <p class="mb-3"><strong>Age:</strong> <?= $age !== '' ? $age : 'N/A' ?> years old</p>
                        <p class="mb-3"><strong>Hobbies:</strong> <?= isset($portfolio['hobbies']) ? htmlspecialchars($portfolio['hobbies']) : 'List your hobbies' ?></p>
                    </div>
                </div>
                <div class="about-image" style="flex: 1; text-align: center; position: relative;">
                    <img src="<?= isset($portfolio['image_path']) ? htmlspecialchars($portfolio['image_path']) : 'default.jpg' ?>" 
                        alt="About Me Image" 
                        class="img-fluid rounded shadow" 
                        style="width: 300px; height: 300px; object-fit: cover; transition: all 0.3s;">
                </div>
            </div>
        </div>
    </section>

    <section class="skills container py-5">
        <div class="container">
            <h2 class="text-center mb-4">Skills</h2>
            <div class="d-flex justify-content-end gap-2 mb-4">
                
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addImageModal">
            <i class="fas fa-plus"></i> Add Image
        </button>
                    <button type="button" class="btn btn-danger btn-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Image" id="delete-mode-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

            <form action="delete_skills.php" method="POST" id="deleteSkillsForm">
            
                <div class="row g-4" id="skills-container">
                    <?php foreach ($skills as $index => $skill): ?>
                        <?php if (!empty($skill['image_skills'])): ?>
                            <div class="col-md-3 d-flex justify-content-center mb-4 position-relative skill-card-wrapper" data-index="<?= $index ?>"> 
                                <div class="card skill-card text-center">
                                    <input 
                                        type="checkbox" 
                                        class="delete-checkbox position-absolute" 
                                        style="top: 10px; left: 10px; display: none;" 
                                        name="skills[]" 
                                        value="<?= htmlspecialchars($skill['skill_id']) ?? '' ?>">
                                    <img 
                                        src="<?= htmlspecialchars($skill['image_skills']) ?? 'default.jpg' ?>" 
                                        class="skill-image" 
                                        alt="<?= htmlspecialchars($skill['skill_name']) ?>" 
                                        style="height: 200px; width: 200px; border: solid #000;">
                                    <h5 class="skill-title mt-3"><?= htmlspecialchars($skill['skill_name']) ?></h5>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    <button id="confirm-delete-btn" type="button" class="btn btn-danger ml-3" style="display: none;" data-bs-toggle="modal" data-bs-target="#confirmModal">Delete</button>
                </div>
            </form>
        </div>
    </section>
    <div class="modal" id="addImageModal" tabindex="-1" aria-labelledby="addImageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addImageModalLabel" style="color:black;">Add Image<p style="color:black;">(JS, Java, etc...)</p></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="add_image.php" id="addImageForm" method="POST" enctype="multipart/form-data">
                        <div id="image-container">
                            <div class="mb-3 image-entry">
                                <label for="image_name" class="form-label" style="color:black;">Image Name</label>
                                <input type="text" class="form-control" name="image_name[]" required>

                                <label for="image" class="form-label" style="color:black;">Select Image</label>
                                <input type="file" class="form-control" name="image[]" required>
                            </div>
                        </div>
                        
                        <button type="button" id="addMoreBtn" class="btn btn-secondary" style="margin: 8px;">Add More</button>
                        <button type="submit" class="btn btn-primary mt-3">Upload Images</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="color:black;">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p style="color:black;">Are you sure you want to delete the selected skills?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="confirmDeletion" form="deleteSkillsForm">Confirm</button>
                </div>
            </div>
        </div>
    </div>


    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModeBtn = document.getElementById('delete-mode-btn');
        const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
        const checkboxes = document.querySelectorAll('.delete-checkbox');
    
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));

        deleteModeBtn.addEventListener('click', function () {
            checkboxes.forEach(checkbox => {
                checkbox.style.display = checkbox.style.display === 'none' ? 'block' : 'none';
            });
            confirmDeleteBtn.style.display = confirmDeleteBtn.style.display === 'none' ? 'block' : 'none';
        });

        confirmDeleteBtn.addEventListener('click', function () {
            const selectedSkills = Array.from(checkboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);

            if (selectedSkills.length === 0) {
                noSelectionModal.show();
                return;
            }

            confirmModal.show();
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const addImageForm = document.getElementById('addImageForm');
        const addMoreBtn = document.getElementById('addMoreBtn');
        const imageContainer = document.getElementById('image-container');
        const addImageModal = new bootstrap.Modal(document.getElementById('addImageModal'));  
        
        let imageEntryHTML = `
            <div class="mb-3 image-entry">
                <label for="image_name" class="form-label">Image Name</label>
                <input type="text" class="form-control" name="image_name[]" required>

                <label for="image" class="form-label">Select Image</label>
                <input type="file" class="form-control" name="image[]" required>
            </div>
        `;

        addMoreBtn.addEventListener('click', function () {
            imageContainer.insertAdjacentHTML('beforeend', imageEntryHTML);
        });
    });

    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    

    
    </body>
    </html>
