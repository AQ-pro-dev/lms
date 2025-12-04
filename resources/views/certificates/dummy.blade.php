<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Appreciation</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: auto;
            padding: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            position: relative;
        }

        .certificate-wrap {
            margin: auto;
            padding: 40px;
            /* border: 10px solid #C6A24D; */
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 3;
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;

        }

        .certificate-topheading {
            text-align: center;
            margin-bottom: 20px;
        }

        .certificate-topheading img {
            max-width: 300px;
        }

        .certificate-content {
            text-align: center;
        }

        .certificate-content h3 {
            color: #C6A24D;
            font-family: 'Century Schoolbook', serif;
            font-size: 26px;
            font-weight: bold;
            margin: 20px 0;
        }

        .certificate-content h4 {
            color: #3A3B4F;
            font-size: 18px;
            font-weight: bold;
        }

        .certificate-content h5 {
            color: #3A3B4F;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }

        .certificate-content p {
            color: #121212;
            font-size: 14px;
            margin: 10px 0;
        }

        .signature-wrap {
            margin-top: 30px;
        }

        .signature-wrap table {
            width: 100%;
            margin-top: 20px;
            text-align: center;
        }

        .signature-wrap td {
            padding: 10px;
            vertical-align: top;
        }

        .signature-wrap img {
            max-height: 60px;
        }

        .footer-label {
            font-size: 16px;
            font-weight: bold;
            color: #C6A24D;
        }

        #watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: 1;
        }
    </style>
</head>

<body>
    <div class="certificate-wrap"
        style="background-image: url('data:image/webp;base64,'{{ base64_encode(file_get_contents(public_path('assets/images/certificate.webp'))) }}');">
        <div class="certificate-topheading">
            <img src="data:image/webp;base64,{{ base64_encode(file_get_contents(public_path('assets/images/logo-img.webp'))) }}"
                alt="Company Logo">
        </div>
        <div class="certificate-content">
            <h3>CERTIFICATE OF APPRECIATION</h3>
            <h4>Is Presented To</h4>
            <h5 class="candidate-name">{{ $user->first_name }} {{ $user->last_name }}</h5>
            <p>For successfully completing the following training program:</p>
            <h5 class="course-title">{{ $course->title }}</h5>
            <p>Credit Hours: [Credit Hours] <br> Date: {{ date('F j, Y') }}</p>
        </div>
        <div class="signature-wrap">
            <table>
                <tr>
                    <td>
                        <img src="signature-tutor.webp" alt="Tutor Signature">
                        <p class="footer-label">Course Tutor</p>
                    </td>
                    <td>
                        <p class="footer-label">Certificate ID</p>
                        <p>[Certificate ID]</p>
                    </td>
                    <td>
                        <img src="signature-ceo.webp" alt="CEO Signature">
                        <p class="footer-label">CEO</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div id="watermark">
        <h2>CONFIDENTIAL</h2>
        <img src="background-placeholder.svg" alt="Watermark">
    </div>
</body>

</html>
