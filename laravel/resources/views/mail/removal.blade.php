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
        .banner {
            position: relative;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
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
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #374151;
            margin-bottom: 20px;
        }
        .notification-title {
            font-size: 24px;
            font-weight: 700;
            color: #dc2626;
            margin: 20px 0;
            text-align: center;
        }
        .info-box {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-left: 4px solid #dc2626;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
        }
        .team-name {
            font-size: 22px;
            font-weight: 700;
            color: #991b1b;
            margin: 0 0 15px;
        }
        .info-text {
            margin: 0;
            color: #991b1b;
            font-size: 15px;
        }
        .details-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
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
        @media only screen and (max-width: 600px) {
            .content {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="banner">
            <h1 class="site-name">RAID Navigator</h1>
            <p class="banner-subtitle">Plateforme de Course d'Orientation</p>
        </div>

        <div class="content">
            <p class="greeting">Bonjour {{ $data['userName'] ?? 'Participant' }},</p>
            
            <h2 class="notification-title">
                Retrait de l'équipe
            </h2>
            
            <div class="info-box">
                <h3 class="team-name">{{ $data['teamName'] }}</h3>
                <p class="info-text">
                    Le manager <strong>{{ $data['managerName'] }}</strong> vous a retiré de l'équipe.
                </p>
            </div>

            <div class="details-box">
                <div class="detail-row">
                    <span class="detail-label">Raid :</span>
                    <span class="detail-value">{{ $data['raidName'] ?? 'Non spécifié' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Course :</span>
                    <span class="detail-value">{{ $data['courseName'] ?? 'Non spécifié' }}</span>
                </div>
            </div>

            <p style="color: #6b7280; font-size: 14px; margin-top: 30px; text-align: center;">
                Si vous avez des questions, n'hésitez pas à contacter le manager de l'équipe.
            </p>
        </div>

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
