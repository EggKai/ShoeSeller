<?php
$title = 'About Us';
include __DIR__ . '/../inc/header.php';
?>
<?php
//TODO: get a better photo :(
$parralaxLayerFG = 'group-photo.png';
$parralaxLayerText = 'ABOUT US';
include __DIR__ . '/../partials/landing.php'; 
?>
<div class="__content all_content">
    <header role="banner">
        <h1>ShoeSeller Web Design</h1>
    </header>

    <nav role="navigation">
        <ul class="nav">
            <li class="nav-item"><a class="nav-link" href="#introduction">Introduction</a></li>
            <li class="nav-item"><a class="nav-link" href="#system-design">System Design</a></li>
            <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
            <li class="nav-item"><a class="nav-link" href="#implementation">Implementation</a></li>
            <li class="nav-item"><a class="nav-link" href="#methodologies">Methodologies</a></li>
            <li class="nav-item"><a class="nav-link" href="#appendix">Appendix</a></li>
        </ul>
    </nav>

    <section id="introduction">
        <h2>1. Introduction</h2>
        <p>
            ShoeSeller is an innovative e-commerce platform specifically tailored for footwear enthusiasts.
            Our mission is to provide customers with a seamless, secure, and engaging online shopping experience.
            Designed with the latest web technologies and robust security practices, ShoeSeller offers a wide
            variety of sneakers, running shoes, and athletic footwear. The platform not only streamlines browsing
            and purchasing but also emphasizes a personalized experience through user reviews, wish lists, and
            tailored product recommendations.
        </p>
        <p>
            Our solution addresses common pain points in online shopping such as slow page loads, confusing
            interfaces, and security vulnerabilities. By leveraging a clean, modern design and a structured
            development approach, we ensure that our customers enjoy a fast, user-friendly, and reliable service.
        </p>
    </section>

    <section id="system-design">
        <h2>2. System Design</h2>
        <h3>2.1 Architecture</h3>
        <p>
            The ShoeSeller website is built using a Model-View-Controller (MVC) architecture. This approach
            segregates data (Model), presentation (View), and user input (Controller), which leads to better code
            organization, easier maintenance, and enhanced scalability. Each component is designed to interact
            seamlessly:
        </p>
        <ul>
            <li><strong>Model:</strong> Manages the data and business logic, handling database interactions (MySQL)
                and operations such as user authentication, product catalog management, and order processing.</li>
            <li><strong>View:</strong> Responsible for rendering the user interface using HTML, CSS, and JavaScript,
                ensuring that the site is responsive and user-friendly across devices.</li>
            <li><strong>Controller:</strong> Acts as the intermediary that processes user requests, manipulates data via Models,
                and sends the appropriate View to the client.</li>
        </ul>
        <h3>2.2 Features (System Design)</h3>
        <p>
            The website supports a multi-role system:
        </p>
        <ul>
            <li><strong>Admin:</strong> Full control over product management, user accounts, orders, discounts, and reporting.</li>
            <li><strong>Employee:</strong> Ability to manage orders, update product listings, and handle customer support tasks.</li>
            <li><strong>User:</strong> Seamless browsing, personalized account management, secure checkout, and interactive product reviews.</li>
        </ul>
    </section>

    <section id="features">
        <h2>3. Features</h2>
        <p>
            ShoeSeller offers an extensive range of features designed to enhance both the shopping experience and
            administrative management:
        </p>
        <ul>
            <li><strong>Product Catalog:</strong> Browse and search through a vast selection of footwear with detailed product descriptions, images, and specifications.</li>
            <li><strong>Shopping Cart & Secure Checkout:</strong> An intuitive shopping cart integrated with Stripe for secure payment processing.</li>
            <li><strong>Role-Based Access Control:</strong> Differentiated functionalities and dashboards for admins, employees, and regular users.</li>
            <li><strong>User Reviews & Ratings:</strong> Authenticated users can write, edit, and view reviews to guide purchasing decisions.</li>
            <li><strong>Discounts & Rewards:</strong> Automatic discount application and a points-based system where every $1 equals 1 point, redeemable as a discount on future purchases.</li>
            <li><strong>Order Management:</strong> Comprehensive order tracking, history view, and the ability to reinitiate pending payments.</li>
            <li><strong>Security:</strong> Implementation of strong security measures including session management, CSRF protection, and robust authentication protocols.</li>
            <li><strong>Responsive Design:</strong> Fully responsive layout ensuring a seamless experience across desktop, tablet, and mobile devices.</li>
        </ul>
    </section>

    <section id="implementation">
        <h2>4. Implementation</h2>
        <p>
            The implementation of ShoeSeller combines modern web technologies and development best practices:
        </p>
        <ul>
            <li><strong>Frontend:</strong> Developed with HTML5, CSS3, and JavaScript. CSS frameworks and custom media queries ensure a responsive and visually appealing design.</li>
            <li><strong>Backend:</strong> Powered by PHP, following the MVC pattern to maintain separation of concerns and ensure code reusability.</li>
            <li><strong>Database:</strong> MySQL is used to manage persistent data, including products, orders, and user information. The schema is carefully designed to enforce data integrity and support scalability.</li>
            <li><strong>Payment Processing:</strong> Stripe integration allows for secure, reliable, and fast payment processing.</li>
            <li><strong>Email Notifications:</strong> PHPMailer is used to send secure and well-formatted email notifications, such as order receipts and password reset links.</li>
            <li><strong>Version Control:</strong> GitHub is used for version control and collaboration, ensuring that the codebase is maintained in a secure, auditable manner.</li>
            <li><strong>Development Environment:</strong> XAMPP provides a local server environment for development and testing.</li>
        </ul>
    </section>

    <section id="methodologies">
        <h2>5. Methodologies</h2>
        <p>
            Throughout the development process, our team employed a combination of methodologies to ensure a robust and high-quality product:
        </p>
        <ul>
            <li><strong>Agile Development:</strong> Iterative development cycles allowed for frequent testing, feedback, and refinement of features.</li>
            <li><strong>User-Centered Design:</strong> Regular user testing and feedback sessions informed UI/UX decisions to ensure the website is intuitive and engaging.</li>
            <li><strong>Security Best Practices:</strong> Implementation of secure coding practices, including input validation, CSRF protection, session management, and regular code reviews.</li>
            <li><strong>Continuous Integration:</strong> Automated tests and regular integration into a shared repository ensured that new code was robust and didn’t break existing functionality.</li>
            <li><strong>Responsive and Adaptive Design:</strong> CSS media queries and flexible grid layouts ensure that the site performs optimally on any device.</li>
        </ul>
    </section>

    <section id="appendix">
        <h2>6. Appendix</h2>
        <p>
            For further details on our project, you can explore the following resources:
        </p>
        <ul>
            <li>Visit our official <a href="https://www.shoeseller.site" target="_blank" rel="noopener">Website</a> for the latest updates and news.</li>
            <li>Check out our project on <a href="https://github.com/EggKai/ShoeSeller" target="_blank" rel="noopener">GitHub</a> for source code, documentation, and commit history.</li>
            <li>Refer to our detailed system documentation and design specifications (available upon request).</li>
            <li>For inquiries, support, or business proposals, please contact our team via the contact page on our website.</li>
        </ul>
    </section>
</div>

<?php
include __DIR__ . '/../inc/footer.php';
?>