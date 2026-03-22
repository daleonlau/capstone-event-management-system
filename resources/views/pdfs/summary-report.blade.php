<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collection Summary Report | Official Financial Statement</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Print & PDF styling - header image on every page */
        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            .page {
                page-break-after: avoid;
                break-inside: avoid-page;
            }
            /* ensure header repeats on each printed page */
            @page {
                margin: 25mm 15mm 20mm 15mm;
                @top-center {
                    content: element(header);
                }
            }
            .pdf-header {
                display: none;
            }
            .print-header-placeholder {
                display: block;
                position: running(header);
                text-align: center;
                margin-bottom: 10px;
            }
            .report-container {
                box-shadow: none;
                padding: 0;
            }
            .professional-table th {
                background-color: #1f5e4a !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .badge-rate, .status-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* screen styles */
        body {
            font-family: 'Inter', 'Segoe UI', 'DejaVu Sans', Roboto, -apple-system, BlinkMacSystemFont, sans-serif;
            background: #eef2f5;
            padding: 30px 24px;
            font-size: 13px;
            line-height: 1.45;
            color: #1a2c3e;
        }

        .report-container {
            max-width: 1280px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* HEADER IMAGE SECTION - using public/images/pdfheader.png */
        .pdf-header {
            background: white;
            text-align: center;
            padding: 20px 24px 12px 24px;
            border-bottom: 2px solid #e2ece7;
        }

        .header-image {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .header-image-img {
            max-height: 100px;
            width: auto;
            object-fit: contain;
        }

        /* if image path exists, we'll use it */
        .print-header-placeholder {
            display: none;
        }

        /* professional summary cards */
        .summary-stats-row {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            padding: 24px 28px 16px 28px;
            background: linear-gradient(to bottom, #ffffff, #fefefb);
            border-bottom: 1px solid #e9efea;
        }

        .stat-card {
            flex: 1;
            min-width: 160px;
            background: #fbfefc;
            border-radius: 20px;
            padding: 18px 12px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2efe8;
            transition: all 0.2s;
        }

        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: #5e8576;
            margin-bottom: 10px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 800;
            color: #1e5a47;
            line-height: 1.2;
        }

        .stat-sub {
            font-size: 13px;
            font-weight: 600;
            color: #2c7a5e;
            margin-top: 6px;
        }

        /* main summary table - clean & presentable */
        .summary-wrapper {
            padding: 8px 28px 20px 28px;
        }

        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin: 20px 0 16px 0;
            border-bottom: 2px solid #dde9e2;
            padding-bottom: 10px;
        }

        .section-title h2 {
            font-size: 20px;
            font-weight: 700;
            color: #1f5e4a;
            letter-spacing: -0.2px;
        }

        .section-title span {
            font-size: 12px;
            background: #f0f6f2;
            padding: 4px 14px;
            border-radius: 40px;
            color: #2a6b55;
        }

        .professional-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .professional-table th {
            background: #1f5e4a;
            color: white;
            font-weight: 700;
            padding: 14px 16px;
            font-size: 13px;
            text-align: left;
        }

        .professional-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #eef3ef;
            vertical-align: middle;
        }

        .professional-table tr:last-child td {
            border-bottom: none;
        }

        .professional-table tr:hover td {
            background-color: #fafefa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 500;
        }

        .badge-rate {
            background: #e2f3e9;
            color: #1a6e4a;
            padding: 5px 12px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 12px;
            display: inline-block;
        }

        .grand-total-row {
            background: #fefae0;
            font-weight: 800;
            border-top: 2px solid #cadfd2;
        }

        .grand-total-row td {
            font-weight: 800;
            background: #fefae0;
        }

        /* student payment table (needs to pay) */
        .students-table-wrapper {
            margin: 16px 28px 28px 28px;
            border-radius: 20px;
            border: 1px solid #e2ede6;
            overflow-x: auto;
            background: white;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .students-table th {
            background: #2c6b56;
            color: white;
            padding: 14px 12px;
            font-weight: 700;
            text-align: left;
        }

        .students-table td {
            padding: 12px 12px;
            border-bottom: 1px solid #eef3ef;
        }

        .status-badge-pending {
            background: #ffe6e2;
            color: #bc3900;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            display: inline-block;
        }

        .footer-signatures {
            padding: 24px 32px 32px 32px;
            background: #fefefc;
            border-top: 1px solid #e2ece5;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 30px;
        }

        .signature-item {
            text-align: center;
            flex: 1;
            min-width: 150px;
        }

        .signature-line {
            border-top: 1px solid #99b7aa;
            width: 180px;
            margin: 35px auto 6px auto;
            padding-top: 5px;
        }

        .signature-name {
            font-weight: 800;
            margin-top: 8px;
            font-size: 13px;
        }

        .signature-title {
            font-size: 11px;
            color: #6e8b7e;
        }

        .report-footer-note {
            font-size: 10px;
            text-align: center;
            padding: 16px 28px 24px;
            background: #fafcfb;
            color: #6f8f86;
            border-top: 1px solid #e2ede6;
        }

        @media (max-width: 700px) {
            .stat-number {
                font-size: 24px;
            }
            .summary-stats-row {
                gap: 12px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<div class="report-container">
    <!-- HEADER IMAGE from public/images/pdfheader.png (visible on screen and print) -->
    <div class="pdf-header">
        <img class="header-image-img" src="public/images/pdfheader.png" alt="University Header" 
             onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<div style=\'padding:12px; background:#f5f9f7; border-radius:12px; color:#2d6a4f;\'>📄 Official Collection Report Header</div>';">
        <!-- fallback text if image missing but style preserved -->
    </div>
    
    <!-- for print running header (repeat on every page) -->
    <div class="print-header-placeholder">
        <img src="public/images/pdfheader.png" alt="Header" style="max-height: 70px; width: auto;">
    </div>

    <!-- PRESENTABLE SUMMARY CARDS (COLLECTION OVERVIEW) -->
    <div class="summary-stats-row" id="summaryStats">
        <div class="stat-card">
            <div class="stat-label">Total Assigned Students</div>
            <div class="stat-number" id="totalStudents">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Need to Pay</div>
            <div class="stat-number" id="needsToPayCount">0</div>
            <div class="stat-sub">with outstanding balance</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Fully Paid</div>
            <div class="stat-number" id="fullyPaidCount">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Collected</div>
            <div class="stat-number" id="totalCollected">₱0</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Outstanding Balance</div>
            <div class="stat-number" id="outstandingBalance">₱0</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Collection Rate</div>
            <div class="stat-number" id="collectionRate">0%</div>
        </div>
    </div>

    <!-- MAIN COLLECTION SUMMARY TABLE (Professional & Presentable) -->
    <div class="summary-wrapper">
        <div class="section-title">
            <h2>📋 COLLECTION SUMMARY BY EVENT / CATEGORY</h2>
            <span>As of March 2026</span>
        </div>
        <table class="professional-table" id="summaryTable">
            <thead>
                <tr>
                    <th>Event / Category</th>
                    <th class="text-right">Assigned Students</th>
                    <th class="text-right">With Balance</th>
                    <th class="text-right">Fully Paid</th>
                    <th class="text-right">Collected (₱)</th>
                    <th class="text-right">Outstanding (₱)</th>
                    <th class="text-right">Collection Rate</th>
                </tr>
            </thead>
            <tbody id="summaryTableBody">
                <tr><td colspan="7" class="text-center" style="padding: 32px;">Loading summary data...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- STUDENTS THAT NEEDS TO PAY (exact format: ID, Name, Amount, Date of Payment, Receipt Number) -->
    <div style="margin: 8px 28px 0 28px;">
        <div class="section-title" style="margin-bottom: 12px;">
            <h2>👥 STUDENTS REQUIRING PAYMENT</h2>
            <span>ID | Name | Amount | Date of Payment | Receipt #</span>
        </div>
    </div>
    <div class="students-table-wrapper">
        <table class="students-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th class="text-right">Amount Due (₱)</th>
                    <th>Date of Payment (Latest)</th>
                    <th>Receipt Number</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody id="studentsNeedsToPayBody">
                <tr><td colspan="6" class="text-center" style="padding: 32px;">Loading student data...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- SIGNATURE & FOOTER -->
    <div class="footer-signatures">
        <div class="signature-item">
            <div class="signature-line"></div>
            <div class="signature-name">MARIA R. SANTOS</div>
            <div class="signature-title">Finance Officer / Treasurer</div>
        </div>
        <div class="signature-item">
            <div class="signature-line"></div>
            <div class="signature-name">DR. ARNEL B. DELA CRUZ</div>
            <div class="signature-title">Student Affairs Adviser</div>
        </div>
        <div class="signature-item">
            <div class="signature-line"></div>
            <div class="signature-name">JOSEFINA M. RAMIREZ</div>
            <div class="signature-title">Organization President</div>
        </div>
    </div>
    <div class="report-footer-note">
        📍 Generated by CSUCC Collection Management System | This report includes all assigned students with pending obligations. 
        The header image appears on every page for official authentication.
    </div>
</div>

<script>
    // ===================== DATA MODEL =====================
    // Students data with full payment details
    // Format includes: ID, Name, Amount Due (remaining), Date of Payment (last), Receipt Number
    const students = [
        { id: "2024-12345", name: "Maria Concepcion A. Dela Cruz", totalFee: 1250, paid: 500, lastPaymentDate: "2026-03-15", receipt: "REC-23891", category: "University Intramurals" },
        { id: "2024-12346", name: "James R. Fernandez", totalFee: 850, paid: 0, lastPaymentDate: "", receipt: "", category: "Cultural Festival" },
        { id: "2024-12347", name: "Sophia Marie L. Gonzales", totalFee: 1200, paid: 1200, lastPaymentDate: "2026-03-05", receipt: "REC-23877", category: "Leadership Summit" },
        { id: "2024-12348", name: "Lucas Andrei M. Rivera", totalFee: 540, paid: 0, lastPaymentDate: "", receipt: "", category: "University Intramurals" },
        { id: "2024-12349", name: "Isabella Rose S. Villanueva", totalFee: 520, paid: 200, lastPaymentDate: "2026-03-18", receipt: "REC-23945", category: "Cultural Festival" },
        { id: "2024-12350", name: "Ethan Gabriel T. Mendoza", totalFee: 950, paid: 950, lastPaymentDate: "2026-03-12", receipt: "REC-23915", category: "Sports Gala" },
        { id: "2024-12351", name: "Chloe Ysabelle C. Ramirez", totalFee: 1675, paid: 0, lastPaymentDate: "", receipt: "", category: "Leadership Summit" },
        { id: "2024-12352", name: "Nathaniel B. Santos", totalFee: 730, paid: 300, lastPaymentDate: "2026-03-20", receipt: "REC-23988", category: "Cultural Festival" },
        { id: "2024-12353", name: "Andrea Mae V. Flores", totalFee: 760, paid: 760, lastPaymentDate: "2026-03-14", receipt: "REC-23933", category: "Sports Gala" },
        { id: "2024-12354", name: "Miguel Angelo D. Cruz", totalFee: 990, paid: 0, lastPaymentDate: "", receipt: "", category: "University Intramurals" },
        { id: "2024-12355", name: "Hannah Marie P. Aguilar", totalFee: 410, paid: 100, lastPaymentDate: "2026-03-19", receipt: "REC-23967", category: "Cultural Festival" },
        { id: "2024-12356", name: "Daniel Joseph R. Lopez", totalFee: 1050, paid: 1050, lastPaymentDate: "2026-03-09", receipt: "REC-23904", category: "Leadership Summit" },
        { id: "2024-12357", name: "Samantha Nicole G. Bautista", totalFee: 1120, paid: 0, lastPaymentDate: "", receipt: "", category: "Sports Gala" }
    ];

    // Helper functions
    const formatCurrency = (val) => `₱${val.toFixed(2)}`;
    const formatDate = (dateStr) => {
        if (!dateStr) return "—";
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
    };

    // compute category summary
    const categoryMap = new Map();
    students.forEach(s => {
        const cat = s.category;
        if (!categoryMap.has(cat)) {
            categoryMap.set(cat, { name: cat, totalStudents: 0, balanceCount: 0, fullyPaid: 0, collected: 0, expected: 0 });
        }
        const entry = categoryMap.get(cat);
        entry.totalStudents++;
        const dueRemaining = s.totalFee - s.paid;
        entry.expected += s.totalFee;
        entry.collected += s.paid;
        if (dueRemaining <= 0) entry.fullyPaid++;
        else entry.balanceCount++;
    });

    // overall stats
    let totalStud = students.length;
    let totalNeedsPay = 0;
    let totalFullyPaid = 0;
    let totalCollectedSum = 0;
    let totalExpectedSum = 0;

    students.forEach(s => {
        totalExpectedSum += s.totalFee;
        totalCollectedSum += s.paid;
        if ((s.totalFee - s.paid) > 0) totalNeedsPay++;
        else totalFullyPaid++;
    });
    const totalOutstanding = totalExpectedSum - totalCollectedSum;
    const collectionRatePercent = totalExpectedSum > 0 ? ((totalCollectedSum / totalExpectedSum) * 100).toFixed(1) : 0;

    // update summary cards
    document.getElementById('totalStudents').innerText = totalStud;
    document.getElementById('needsToPayCount').innerText = totalNeedsPay;
    document.getElementById('fullyPaidCount').innerText = totalFullyPaid;
    document.getElementById('totalCollected').innerHTML = formatCurrency(totalCollectedSum);
    document.getElementById('outstandingBalance').innerHTML = formatCurrency(totalOutstanding);
    document.getElementById('collectionRate').innerHTML = `${collectionRatePercent}%`;

    // Build professional summary table (collection summary by category)
    const summaryBody = document.getElementById('summaryTableBody');
    summaryBody.innerHTML = '';
    for (let [_, cat] of categoryMap.entries()) {
        const rate = cat.expected > 0 ? ((cat.collected / cat.expected) * 100).toFixed(1) : 0;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td style="font-weight: 600;">${cat.name}</td>
            <td class="text-right">${cat.totalStudents}</td>
            <td class="text-right">${cat.balanceCount}</td>
            <td class="text-right">${cat.fullyPaid}</td>
            <td class="text-right font-mono">${formatCurrency(cat.collected)}</td>
            <td class="text-right font-mono">${formatCurrency(cat.expected - cat.collected)}</td>
            <td class="text-right"><span class="badge-rate">${rate}%</span></td>
        `;
        summaryBody.appendChild(row);
    }
    // grand total row
    const grandRow = document.createElement('tr');
    grandRow.classList.add('grand-total-row');
    grandRow.innerHTML = `
        <td style="font-weight: 800;">📌 GRAND TOTAL (All Categories)</td>
        <td class="text-right font-bold">${totalStud}</td>
        <td class="text-right font-bold">${totalNeedsPay}</td>
        <td class="text-right font-bold">${totalFullyPaid}</td>
        <td class="text-right font-mono" style="font-weight:800;">${formatCurrency(totalCollectedSum)}</td>
        <td class="text-right font-mono" style="font-weight:800;">${formatCurrency(totalOutstanding)}</td>
        <td class="text-right"><span class="badge-rate" style="background:#e2e6cf;">${collectionRatePercent}%</span></td>
    `;
    summaryBody.appendChild(grandRow);

    // ********** DISPLAY ALL STUDENTS THAT NEED TO PAY **********
    // Format: ID, Name, Amount (remaining due), Date of Payment, Receipt Number
    const needsToPayStudents = students.filter(s => (s.totalFee - s.paid) > 0);
    const tbodyNeeds = document.getElementById('studentsNeedsToPayBody');
    tbodyNeeds.innerHTML = '';
    
    if (needsToPayStudents.length === 0) {
        tbodyNeeds.innerHTML = '<tr><td colspan="6" class="text-center" style="padding: 40px;">✅ All assigned students are fully paid. No outstanding balances.</td></tr>';
    } else {
        needsToPayStudents.forEach(student => {
            const amountDue = student.totalFee - student.paid;
            const paymentDate = student.lastPaymentDate && student.lastPaymentDate !== "" ? formatDate(student.lastPaymentDate) : "—";
            const receiptDisplay = student.receipt && student.receipt !== "" ? student.receipt : "—";
            const statusText = (student.paid > 0 && amountDue > 0) ? "Partial" : "Unpaid";
            const row = document.createElement('tr');
            row.innerHTML = `
                <td style="font-family: monospace;">${student.id}</td>
                <td style="font-weight: 500;">${student.name}</td>
                <td class="text-right" style="font-weight: 700; color:#b6421a;">${formatCurrency(amountDue)}</td>
                <td>${paymentDate}</td>
                <td>${receiptDisplay}</td>
                <td class="text-center"><span class="status-badge-pending">${statusText}</span></td>
            `;
            tbodyNeeds.appendChild(row);
        });
    }

    // Additional note: Show count in summary note
    const noteDiv = document.createElement('div');
    noteDiv.style.margin = "12px 28px 0 28px";
    noteDiv.style.fontSize = "12px";
    noteDiv.style.background = "#F4FBF7";
    noteDiv.style.padding = "10px 18px";
    noteDiv.style.borderRadius = "40px";
    noteDiv.style.display = "inline-block";
    noteDiv.innerHTML = `📌 <strong>Collection Summary Insight:</strong> ${totalNeedsPay} student(s) require payment. Total outstanding: ${formatCurrency(totalOutstanding)}. Collection rate: ${collectionRatePercent}% across all events.`;
    const summaryWrapperDiv = document.querySelector('.summary-wrapper');
    if (summaryWrapperDiv) {
        summaryWrapperDiv.insertAdjacentElement('afterend', noteDiv);
    }
    
    // ensure that the header image repeats on every printed page by using @page + element
    // for screen, it's visible above.
    const stylePrint = document.createElement('style');
    stylePrint.textContent = `
        @media print {
            .pdf-header {
                display: none;
            }
            .print-header-placeholder {
                display: block;
                text-align: center;
                margin-bottom: 12px;
                content: normal;
            }
            .report-container {
                margin: 0;
                padding-top: 0;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .summary-stats-row, .professional-table, .students-table {
                break-inside: avoid;
            }
        }
    `;
    document.head.appendChild(stylePrint);

    // add fallback for missing image: still user can see official header
    const headerImg = document.querySelector('.header-image-img');
    if (headerImg) {
        headerImg.addEventListener('error', function() {
            this.style.display = 'none';
            const parent = this.parentElement;
            const fallbackSpan = document.createElement('div');
            fallbackSpan.style.padding = '12px';
            fallbackSpan.style.background = '#eef3ef';
            fallbackSpan.style.borderRadius = '12px';
            fallbackSpan.style.fontWeight = '600';
            fallbackSpan.style.color = '#2a6b4e';
            fallbackSpan.innerText = '🏛️ CARAGA STATE UNIVERSITY - OFFICIAL COLLECTION REPORT';
            parent.appendChild(fallbackSpan);
        });
    }
</script>
</body>
</html>