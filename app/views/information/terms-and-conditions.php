<?php
$title = 'T&C';
include __DIR__ . '/../inc/header.php';
?>

    <h1>Terms and Conditions</h1>
    <p>These are the terms and conditions of our website.</p>

    <div class="dropdown" onclick="toggleDropdown('section1', this)">
    <h2> 1. SCOPE OF APPLICATION</h2>
    </div>
    <div id="section1" class="content">
    <ol>
       <li> 1.1     These general terms and conditions of sale (the “General Terms and Conditions”) apply to all sales contracts entered into between Off-White Operating S.r.l., with registered office in via Turati, 12, Milan, 20121 (MI), VAT No. 08436950961, registered in the Companies Registry of Milan under No. 2026193, subject to the management and coordination of New Guards Group Holding S.p.A. (“Off-White” or the “Company”) and the customer, identified as a consumer under the applicable consumer legislation (the “Customer”). Off-White and the Customer are hereinafter jointly referred to as the “Parties” and individually as “Party”.</li>

       <li> 1.2     The Customer must read carefully and accept (by clicking on the appropriate flag in the shopping cart checkout phase) these General Terms and Conditions. By accepting the General Terms and Conditions, the Customer undertakes to comply with their provisions. If the Customer does not accept the General Terms and Conditions, they may not purchase “Off-White” branded products. Therefore, the Customer is invited to print and save a copy of the General Terms and Conditions for future reference.</li>

       <li>1.3     Off-White is part of the Farfetch group (a UK platform active in online sales of fashion, luxury and design goods, “Farfetch”): as better described below, Farfetch manages certain services on behalf of Off-White which are connected to the purchase of “Off-White” branded products and, specifically, payment processing and invoicing, product delivery services and an after-sales assistance service to Customers. The after-sales service will be managed by Farfetch and may be provided to the Customer also in a language other than that of the country in which the consumer resides or of which they are a citizen (such as, for example, English).</li>

       <li>1.4     The General Terms and Conditions govern the manner in which the Company sells “Off-White” branded products (as well as any digital content and/or services, the “Products”) via the e-commerce website www.off---white.com(the “Website”).</li>

       <li>1.5     The General Terms and Conditions do not govern the provision of services or the sale of products by parties other than Off-White and Farfetch, even if they are present on the Website through links, banners or other hypertext links.</li>

       <li>1.6     Off-White can always be contacted at ecommerce@off---white.com and on +44 808 196 1114.</li>

       <li>1.7     Purchasing Products through the Website is strictly reserved for persons who: </li>

        <li>(i)    have legal capacity and have reached the legal age in their country of residence; and</li>

        <li>(ii)   purchase the Products for personal use and not for purposes related to commercial, business or professional activities.</li>

        <li>1.8     Off-White will not accept and/or process orders from channels other than the Website or from persons who do not meet the requirements set out in the above paragraph. </li>
    </ol>
    </div>

    <h2> 2. CONCLUSION OF THE AGREEMENT</h2>
    <h2> 3. PRODUCT AVAILABILITY</h2>
    <h2> 4. PURCHASE PROCEDURE</h2>
    <h2> 5. PRICE AND PAYMENT METHOD</h2>
    <h2> 6. SHIPPING AND DELIVERY</h2>
    <h2> 7. RISKS AND OWNERSHIP</h2>
    <h2> 8. CUSTOMIZATION AND PRE-ORDERS</h2>
    <h2> 9. WARRANTY</h2>
    <h2> 10. FORCE MAJEURE</h2>
    <h2> 11. RIGHT OF WITHDRAWAL</h2>
    <h2> 12. INTELLECTUAL PROPERTY</h2>
    <h2> 13. PERSONAL DATA COLLECTION</h2>
    <h2> 14. NOTICES</h2>
    <h2> 15. CHANGES AND UPDATES</h2>
    <h2> 16. MISCELLANEOUS PROVISIONS</h2>
    <h2> 17. APPLICABLE LAW AND JUSTIFICATION</h2>

    <script>
        function toggleDropdown(id, element) {
            var content = document.getElementById(id);
            var symbol = element.querySelector("span");

            // Check if the content is currently visible
            if (content.style.display === "block") {
                content.style.display = "none";
                symbol.textContent = "+";  // Change symbol to "+"
            } else {
                content.style.display = "block";
                symbol.textContent = "-";  // Change symbol to "-"
            }
        }
    </script>


<?php
include __DIR__ . '/../inc/footer.php';
?>