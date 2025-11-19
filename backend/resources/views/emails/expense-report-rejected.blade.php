<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de dépenses rejeté</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f7fa;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 40px 30px; text-align: center; border-radius: 8px 8px 0 0;">
                            <div style="font-size: 48px; margin-bottom: 10px;">✗</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 600;">
                                Rapport Rejeté
                            </h1>
                            <p style="margin: 10px 0 0 0; color: #ffffff; font-size: 14px; opacity: 0.9;">
                                Votre rapport de dépenses nécessite des modifications
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px 0; color: #666; font-size: 16px; line-height: 1.6;">
                                Bonjour <strong>{{ $report->user->name }}</strong>,
                            </p>

                            <p style="margin: 0 0 20px 0; color: #666; font-size: 16px; line-height: 1.6;">
                                Votre rapport de dépenses a été examiné et nécessite des corrections avant d'être approuvé.
                            </p>

                            <!-- Report Details Card -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #fef2f2; border: 2px solid #ef4444; border-radius: 6px; margin: 30px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="padding: 8px 0; color: #888; font-size: 14px; width: 40%;">
                                                    Référence :
                                                </td>
                                                <td style="padding: 8px 0; color: #333; font-size: 14px; font-weight: 600;">
                                                    {{ $report->reference }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #888; font-size: 14px;">
                                                    Titre :
                                                </td>
                                                <td style="padding: 8px 0; color: #333; font-size: 14px;">
                                                    {{ $report->title }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #888; font-size: 14px;">
                                                    Montant :
                                                </td>
                                                <td style="padding: 8px 0; color: #ef4444; font-size: 18px; font-weight: 700;">
                                                    {{ number_format($report->total_amount, 3) }} TND
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #888; font-size: 14px;">
                                                    Rejeté par :
                                                </td>
                                                <td style="padding: 8px 0; color: #333; font-size: 14px; font-weight: 600;">
                                                    {{ $report->rejecter->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #888; font-size: 14px;">
                                                    Date de rejet :
                                                </td>
                                                <td style="padding: 8px 0; color: #333; font-size: 14px;">
                                                    {{ $report->rejected_at->format('d/m/Y à H:i') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Rejection Reason -->
                            <div style="margin: 20px 0; padding: 20px; background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                                <h3 style="margin: 0 0 10px 0; color: #856404; font-size: 16px; font-weight: 600;">
                                    Raison du rejet :
                                </h3>
                                <p style="margin: 0; color: #856404; font-size: 14px; line-height: 1.6;">
                                    {{ $report->rejection_reason }}
                                </p>
                            </div>

                            <!-- Action Steps -->
                            <div style="margin: 30px 0; padding: 20px; background-color: #f8f9fa; border-radius: 6px;">
                                <h3 style="margin: 0 0 15px 0; color: #333; font-size: 16px; font-weight: 600;">
                                    Que faire maintenant ?
                                </h3>
                                <ol style="margin: 0; padding-left: 20px; color: #666; font-size: 14px; line-height: 1.8;">
                                    <li>Consultez votre rapport et lisez attentivement la raison du rejet</li>
                                    <li>Apportez les corrections nécessaires</li>
                                    <li>Ajoutez les justificatifs manquants si demandé</li>
                                    <li>Soumettez à nouveau votre rapport pour approbation</li>
                                </ol>
                            </div>

                            <!-- Action Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $reportUrl }}" style="display: inline-block; padding: 14px 40px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 6px rgba(239, 68, 68, 0.3);">
                                            Corriger le rapport
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 20px 0 0 0; color: #999; font-size: 13px; line-height: 1.6;">
                                Si vous avez des questions concernant ce rejet, n'hésitez pas à contacter {{ $report->rejecter->name }}.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0 0 10px 0; color: #999; font-size: 12px;">
                                © {{ date('Y') }} Compteo TN - Tous droits réservés
                            </p>
                            <p style="margin: 0; color: #999; font-size: 12px;">
                                Cet email a été envoyé automatiquement, merci de ne pas y répondre.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
