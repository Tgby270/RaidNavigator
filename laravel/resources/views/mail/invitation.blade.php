<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        /* Top Banner - Like Welcome Page */
        .banner {
            position: relative;
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        .banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: repeating-linear-gradient(
                45deg,
                rgba(255, 255, 255, 0.05),
                rgba(255, 255, 255, 0.05) 10px,
                transparent 10px,
                transparent 20px
            );
            opacity: 0.3;
        }
        .logo-container {
            position: relative;
            width: 80px;
            height: 80px;
            background-color: #10b981;
            border-radius: 16px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            color: white;
        }
        .site-name {
            position: relative;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .banner-subtitle {
            position: relative;
            font-size: 18px;
            font-weight: 500;
            margin: 10px 0 0;
            opacity: 0.95;
        }
        /* Content Section */
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #374151;
            margin-bottom: 20px;
        }
        .invitation-title {
            font-size: 24px;
            font-weight: 700;
            color: #059669;
            margin: 20px 0;
            text-align: center;
        }
        .team-info-box {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-left: 4px solid #10b981;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
        }
        .team-name {
            font-size: 22px;
            font-weight: 700;
            color: #065f46;
            margin: 0 0 15px;
        }
        .course-details {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .course-details h3 {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin: 0 0 15px;
            border-bottom: 2px solid #10b981;
            padding-bottom: 8px;
        }
        .detail-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #6b7280;
            min-width: 140px;
        }
        .detail-value {
            color: #1f2937;
            font-weight: 500;
        }
        /* Action Buttons */
        .action-section {
            text-align: center;
            margin: 40px 0;
            padding: 30px 20px;
            background-color: #f9fafb;
            border-radius: 12px;
        }
        .action-text {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 25px;
            font-weight: 500;
        }
        .button-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 28px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 2px solid transparent;
            min-width: 180px;
            color: #ffffff !important;
        }
        .btn-accept {
            background: linear-gradient(135deg, #00d97e 0%, #00b86b 100%);
            border-color: #00a85e;
        }
        .btn-accept:hover {
            background: linear-gradient(135deg, #00b86b 0%, #00a85e 100%);
            box-shadow: 0 6px 16px rgba(0, 217, 126, 0.5);
            transform: translateY(-2px);
        }
        .btn-decline {
            background: linear-gradient(135deg, #ff4757 0%, #ff3838 100%);
            border-color: #ee1f35;
        }
        .btn-decline:hover {
            background: linear-gradient(135deg, #ff3838 0%, #ee1f35 100%);
            box-shadow: 0 6px 16px rgba(255, 71, 87, 0.5);
            transform: translateY(-2px);
        }
        /* Footer */
        .footer {
            background-color: #1f2937;
            color: #9ca3af;
            text-align: center;
            padding: 30px 20px;
            font-size: 14px;
        }
        .footer-logo {
            font-size: 18px;
            font-weight: 700;
            color: #10b981;
            margin-bottom: 10px;
        }
        .icon {
            width: 18px;
            height: 18px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 6px;
        }
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .content {
                padding: 30px 20px;
            }
            .button-container {
                flex-direction: column;
                gap: 10px;
            }
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Top Banner -->
        <div class="banner">
            <h1 class="site-name">RAID Navigator</h1>
            <p class="banner-subtitle">Plateforme de Course d'Orientation</p>
        </div>

        <!-- Main Content -->
        <div class="content">
            <p class="greeting">Bonjour {{ $data['userName'] ?? 'Participant' }},</p>
            
            <h2 class="invitation-title">
                Vous êtes invité(e) à rejoindre une équipe !
            </h2>
            
            <div class="team-info-box">
                <h3 class="team-name">{{ $data['teamName'] }}</h3>
                <p style="margin: 0; color: #065f46; font-size: 15px;">
                    Le manager <strong>{{ $data['managerName'] }}</strong> vous invite à faire partie de son équipe pour participer à une course passionnante.
                </p>
            </div>

            <!-- Course Details -->
            <div class="course-details">
                <h3>
                    Détails de la Course
                </h3>
                <div class="detail-row">
                    <span class="detail-label">
                        Raid :
                    </span>
                    <span class="detail-value">{{ $data['raidName'] ?? 'Non spécifié' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">
                        Course :
                    </span>
                    <span class="detail-value">{{ $data['courseName'] ?? 'Non spécifié' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">
                        Date :
                    </span>
                    <span class="detail-value">{{ $data['courseDate'] ?? 'À déterminer' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">
                        Heure de départ :
                    </span>
                    <span class="detail-value">{{ $data['courseStartTime'] ?? 'À déterminer' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">
                        Distance :
                    </span>
                    <span class="detail-value">{{ $data['courseDistance'] ?? 'Non spécifié' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">
                        Places équipe :
                    </span>
                    <span class="detail-value">{{ $data['currentMembers'] ?? '0' }}/{{ $data['maxCapacity'] ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-section">
                <p class="action-text">
                    <strong>Prêt(e) à relever le défi ?</strong><br>
                    Rejoignez l'équipe et participez à cette aventure passionnante !
                </p>
                <div class="button-container">
                    <a href="{{ url('/invitation/accept?team=' . urlencode($data['teamId'] ?? '') . '&raid=' . urlencode($data['raidId'] ?? '') . '&course=' . urlencode($data['courseId'] ?? '') . '&email=' . urlencode($data['email'] ?? '')) }}" 
                       class="btn btn-accept">
                        Accepter
                    </a>
                    <a href="{{ url('/invitation/decline?team=' . urlencode($data['teamId'] ?? '') . '&raid=' . urlencode($data['raidId'] ?? '') . '&course=' . urlencode($data['courseId'] ?? '') . '&email=' . urlencode($data['email'] ?? '')) }}" 
                       class="btn btn-decline">
                        Refuser
                    </a>
                </div>
            </div>

            <p style="color: #6b7280; font-size: 14px; margin-top: 30px; text-align: center;">
                Si vous avez des questions, n'hésitez pas à contacter le manager de l'équipe.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-logo">RAID Navigator</div>
            <p style="margin: 5px 0;">Votre plateforme de gestion de courses d'orientation</p>
            <p style="margin: 15px 0 5px; font-size: 12px;">
                Cet email a été envoyé automatiquement. Veuillez ne pas y répondre directement.
            </p>
        </div>
    </div>
</body>
</html>