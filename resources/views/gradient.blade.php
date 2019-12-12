<html>
<head>

    <style>
        svg {
            transform: rotate(45deg);

            border: 1px solid red;
        }

        #wrap {
            background: linear-gradient(yellow, blue);
            clip-path: url(#gradient);
        }

        .qrcode {
            transform: rotate(45deg);
            width: 300px;
            background: linear-gradient(yellow, blue);
        }

        .row {
            clear: both;
        }

        .cell {
            visibility: hidden;
            float: left;
            width: 5px;
            height: 5px;
            border: 1px solid red;
            background: red;
            border-radius: 3px;
        }

        .cell.visible {
            visibility: visible;
        }

    </style>

</head>
<body>


{{--{{$qrcode->render($qrcode)}}--}}

{{--{{ (new \chillerlan\QRCode\QRCode($options))->render($data)}}--}}

</body>
</html>
