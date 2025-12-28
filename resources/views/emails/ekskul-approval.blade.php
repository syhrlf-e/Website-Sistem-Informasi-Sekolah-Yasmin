<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <title>Pendaftaran Ekskul Diterima</title>
    <style>
        @media (prefers-color-scheme: dark) {
            .email-wrapper {
                background-color: #1a1a2e !important;
            }

            .email-body {
                background-color: #16213e !important;
            }

            .text-dark {
                color: #f3f4f6 !important;
            }

            .text-muted {
                color: #9ca3af !important;
            }

            .detail-section {
                background-color: #1e293b !important;
                border-color: #334155 !important;
            }

            .detail-header {
                border-color: #334155 !important;
            }

            .detail-row {
                border-color: #334155 !important;
            }
        }

        details summary {
            cursor: pointer;
            list-style: none;
        }

        details summary::-webkit-details-marker {
            display: none;
        }

        details[open] .arrow {
            transform: rotate(180deg);
        }
    </style>
</head>

<body
    style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f5f5f5;">
    <table class="email-wrapper" role="presentation" style="width: 100%; background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table class="email-body" role="presentation"
                    style="width: 100%; max-width: 600px; background-color: #ffffff;">

                    <!-- Header with Logo -->
                    <tr>
                        <td style="padding: 24px 12px; border-bottom: 1px solid #e5e7eb;">
                            <table role="presentation" style="width: 100%;">
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <img src="{{ config('app.url') }}/img/logo_yasmin.png" alt="Logo"
                                            style="width: 48px; height: 48px; border-radius: 8px; vertical-align: middle; margin-right: 12px;">
                                        <span class="text-dark"
                                            style="font-size: 16px; font-weight: 600; color: #111827; vertical-align: middle;">SMA
                                            Mutiara Insan Nusantara</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 32px 12px;">
                            <!-- Greeting -->
                            <p class="text-muted" style="margin: 0 0 24px 0; color: #6b7280; font-size: 15px;">
                                Hai {{ $registration->nama_lengkap }},
                            </p>

                            <!-- Headline -->
                            <h1 class="text-dark"
                                style="margin: 0 0 24px 0; color: #111827; font-size: 24px; font-weight: 700; line-height: 1.3;">
                                Selamat, pendaftaran kamu diterima!
                            </h1>

                            <!-- Description -->
                            <p class="text-muted"
                                style="margin: 0 0 16px 0; color: #6b7280; font-size: 15px; line-height: 1.7;">
                                Kami dengan senang hati mengkonfirmasi bahwa kamu telah resmi bergabung dalam keluarga
                                <strong class="text-dark" style="color: #111827;">{{ $ekskul->nama }}</strong>.
                            </p>
                            <p class="text-muted"
                                style="margin: 0 0 32px 0; color: #6b7280; font-size: 15px; line-height: 1.7;">
                                Selamat datang! Persiapkan dirimu untuk pengalaman seru bersama kami.
                            </p>
                        </td>
                    </tr>

                    <!-- Detail Pendaftaran Section -->
                    <tr>
                        <td style="padding: 0 12px 32px 12px;">
                            <table class="detail-section" role="presentation"
                                style="width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                                <!-- Section Header -->
                                <tr>
                                    <td colspan="2" class="detail-header"
                                        style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb;">
                                        <p class="text-dark"
                                            style="margin: 0; color: #111827; font-size: 14px; font-weight: 600;">Detail
                                            Pendaftaran</p>
                                    </td>
                                </tr>
                                <!-- Nama -->
                                <tr>
                                    <td class="detail-row text-muted"
                                        style="padding: 14px 20px; color: #6b7280; font-size: 14px; border-bottom: 1px solid #f3f4f6; width: 45%;">
                                        Nama</td>
                                    <td class="detail-row text-dark"
                                        style="padding: 14px 20px; color: #111827; font-size: 14px; font-weight: 500; text-align: right; border-bottom: 1px solid #f3f4f6;">
                                        {{ $registration->nama_lengkap }}
                                    </td>
                                </tr>
                                <!-- Kelas -->
                                <tr>
                                    <td class="detail-row text-muted"
                                        style="padding: 14px 20px; color: #6b7280; font-size: 14px; border-bottom: 1px solid #f3f4f6;">
                                        Kelas</td>
                                    <td class="detail-row text-dark"
                                        style="padding: 14px 20px; color: #111827; font-size: 14px; font-weight: 500; text-align: right; border-bottom: 1px solid #f3f4f6;">
                                        {{ $registration->kelas }}
                                    </td>
                                </tr>
                                <!-- Ekstrakurikuler -->
                                <tr>
                                    <td class="text-muted" style="padding: 14px 20px; color: #6b7280; font-size: 14px;">
                                        Ekstrakurikuler</td>
                                    <td class="text-dark"
                                        style="padding: 14px 20px; color: #111827; font-size: 14px; font-weight: 500; text-align: right;">
                                        {{ $ekskul->nama }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- WhatsApp CTA Section -->
                    <tr>
                        <td style="padding: 0 12px 32px 12px; text-align: center;">
                            <p class="text-muted"
                                style="margin: 0 0 20px 0; color: #6b7280; font-size: 15px; line-height: 1.6;">
                                Silakan hubungi admin untuk bergabung ke grup.
                            </p>
                            <a href="{{ $whatsappUrl }}"
                                style="display: inline-block; background-color: #25D366; color: #111827; padding: 16px 32px; text-decoration: none; border-radius: 50px; font-weight: 400; font-size: 18px;">
                                Hubungi Admin
                            </a>
                        </td>
                    </tr>

                    <!-- Closing -->
                    <tr>
                        <td style="padding: 0 12px 32px 12px; border-top: 1px solid #e5e7eb;">
                            <p class="text-muted"
                                style="margin: 24px 0 0 0; color: #6b7280; font-size: 14px; line-height: 1.7;">
                                Terima kasih telah bergabung bersama kami.<br>
                                Salam hangat,<br>
                                <strong class="text-dark" style="color: #111827;">SMA Mutiara Insan Nusantara</strong>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>