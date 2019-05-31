<!DOCTYPE html>
<html lang="en">
<head>
    <title>Hoanh Anh Website</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon.ico">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <style>
        .section {
            background-size: cover;
            height: 100%;
            height: 100vh;
            position: relative;
        }

        .section:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            background-color: #333;
            bottom: 0;
            z-index: -2;
        }

        @media screen and (max-width: 767px) {
            .section.section-select {
                padding: 0;
            }
        }

        .section .loginbtn {
            height: 50px;
            width: 100%;
            -webkit-border-radius: 25px;
            border-radius: 25px;
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            font-style: normal;
            font-stretch: normal;
            line-height: 1.2;
            letter-spacing: 0.3px;
            color: #72440a;
            border: 0;
            background-image: linear-gradient(to left, #e0c65a, #e0c65a 15%, #f8e884 50%, #e0c65a 87%, #e0c65a);

        }

        .section .loginbtn:focus {
            border: 0;
            outline: 0;
        }

        .section .title-select {
            font-size: 32px;
            color: #fff;
            line-height: 1.25;
            padding-top: 60px;
            padding-bottom: 30px;
            position: relative;
            z-index: -1;
        }

        /* .section .title-select.open {
			display: none;
		} */

        .section .select .dropdown {
            height: 55px;
            width: 100%;
            -webkit-border-radius: 4px;
            border-radius: 4px;
            line-height: 55px;
            position: relative;
            font-size: 16px;
            box-shadow: 0 3px 10px 0 rgba(113, 73, 12, 0.31);
            background-color: #ffffff;
        }

        /* .section .select .dropdown.open {
			margin-top:-60px;
		}

		.section .select .dropdown.open .list {
			top: -172%;
		} */

        /* select {
		  display: none;
		} */

        .wrapper {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            display: flex;
            -webkit-box-pack: justify;
            -webkit-justify-content: space-between;
            -ms-flex-pack: justify;
            justify-content: space-between;
            -webkit-flex-wrap: wrap;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            position: relative;
            height: 100vh;
            padding: 0 36px 0;
        }

        .wrapper:after {
            content: '';
            position: absolute;
            width: 130px;
            height: 5px;
            border-radius: 4px;
            background-color: #8e8e93;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
        }

        @media screen and (min-width: 767px) {
            .wrapper {
                max-width: 500px;
                width: 100%;
                margin: 0 auto;
                padding: 0;

            }
        }

        .section-select .close {
            position: absolute;
            right: 20px;
            top: 13px;
            width: 12px;
            height: 12px;
            opacity: 1;
        }

        .section-select .close:hover {
            opacity: 1;
        }

        .section-select .close:before, .section-select .close:after {
            position: absolute;
            left: 15px;
            content: ' ';
            height: 20px;
            width: 2px;
            background-color: #be8a4a;
        }

        .section-select .close:before {
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .section-select .close:after {
            -webkit-transform: rotate(-45deg);
            -ms-transform: rotate(-45deg);
            transform: rotate(-45deg);
        }

        .section-select .dropdown,
        .section-select .clearfix,
        .section-select .select {
            display: inline-block;
            width: 100%;
            -webkit-box-align: end;
            -webkit-align-items: flex-end;
            -ms-flex-align: end;
            align-items: flex-end;

        }

        .section-select .clearfix {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            margin-bottom: 40px;
            position: relative;
            z-index: -2;
        }

        .dropdown {
            background-image: -webkit-linear-gradient(top, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 100%);
            background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(255, 255, 255, 0.25)), to(rgba(255, 255, 255, 0)));
            background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 100%);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#40FFFFFF', endColorstr='#00FFFFFF', GradientType=0);
            background-color: #f6f6f6;
            -webkit-border-radius: 6px;
            border-radius: 6px;
            border: solid 1px #eee;
            -webkit-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.0075);
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.0075);
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            cursor: pointer;
            display: block;
            float: left;
            font-size: 14px;
            font-weight: normal;
            height: 42px;
            line-height: 40px;
            outline: none;
            padding-left: 18px;
            padding-right: 30px;
            text-align: left !important;
            -webkit-transition: all 0.2s ease-in-out;
            transition: all 0.2s ease-in-out;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            white-space: nowrap;
            width: auto;
            -webkit-box-shadow: 0 3px 10px #71490C;
            box-shadow: 0 3px 10px #71490C;
        }

        .dropdown:focus {
            background-color: #fff;
        }

        .dropdown:hover {
            background-color: #fff;
        }

        .dropdown ul, ol {
            list-style: none;
        }

        .dropdown:active, .dropdown.open {
            background-color: #fff !important;
            border-color: #fff;
            -webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05) inset;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05) inset;
        }

        .dropdown:after {
            height: 0;
            width: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid #777;
            -webkit-transform: origin(50% 20%);
            -ms-transform: origin(50% 20%);
            transform: origin(50% 20%);
            -webkit-transition: all 0.125s ease-in-out;
            transition: all 0.125s ease-in-out;
            content: '';
            display: block;
            pointer-events: none;
            position: absolute;
            right: 50px;
            top: 43%;
        }

        @media screen and (min-width: 767px) {
            .dropdown:after {
                right: 18px;
                top: 43.5%;
            }
        }

        .dropdown.open:after {
            -webkit-transform: rotate(-180deg);
            -ms-transform: rotate(-180deg);
            transform: rotate(-180deg);
        }

        .dropdown.open .list {
            -webkit-transform: scale(1);
            -ms-transform: scale(1);
            transform: scale(1);
            opacity: 1;
            pointer-events: auto;
        }

        .dropdown.open .option {
            cursor: pointer;
        }

        .dropdown.wide {
            width: 100%;
        }

        .dropdown.wide .list {
            left: 0 !important;
            right: 0 !important;
        }

        .dropdown .list {
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            -webkit-border-radius: 6px;
            border-radius: 6px;
            opacity: 0;
            overflow: hidden;
            pointer-events: none;
            width: -webkit-calc(100% + 30px);
            width: calc(100% + 30px);
            position: absolute;
            top: -300%;
            left: -15px;
            right: -15px;
        }

        .section .select .dropdown:before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            top: 0;
            z-index: -1;
            background-color: #fff;
            width: -webkit-calc(100% + 30px);
            width: calc(100% + 30px);
            position: absolute;
            top: -300%;
            left: -15px;
            right: -15px;
            height: 460px;
            opacity: 0;
            -webkit-transition: all 0.2s ease-in-out;
            transition: all 0.2s ease-in-out;
        }

        .section .select .dropdown.open:before {
            webkit-transform: scale(1);
            -ms-transform: scale(1);
            transform: scale(1);
            pointer-events: auto;
            opacity: 1;
        }
        @media screen and (min-width: 767px) {
            .dropdown .list {
                width: 100%;
                position: absolute;
                top: 65px;
                left: 0px;
                right: 0px;
            }
        }

        .dropdown .list:hover .option:not(:hover) {
            background-color: transparent !important;
        }

        .dropdown .list ul {
            height: 350px;
            overflow: auto;
            padding: 0;
        }

        .dropdown .list .title-select-modal {
            font-size: 18px;
            font-weight: bold;
            font-style: normal;
            font-stretch: normal;
            letter-spacing: normal;
            text-align: left;
            color: #1d1103;
            padding: 20px;
            line-height: 1.25;
            font-weight: bold;
        }

        .dropdown .option {
            cursor: default;
            font-weight: 400;
            line-height: 40px;
            outline: none;
            padding: 0 20px;
            text-align: left;
            -webkit-transition: all 0.2s;
            transition: all 0.2s;
        }

        .dropdown .option:hover, .dropdown .option:focus {
            background-color: #f6f6f6 !important;
        }

        .dropdown .option.selected {
            font-weight: 600;
        }

        .dropdown .option.selected:focus {
            background: #f6f6f6;
        }

        .section .select2-container {
            width: 100% !important;
            height: 55px;
            border-radius: 4px;
            box-shadow: 0 3px 10px 0 rgba(113, 73, 12, 0.31);
            background-color: #ffffff;
            z-index: 1100;
        }
        .section .select2-container.select2-container--open {
            z-index: -2;
        }
        .section .select2-container .select2-selection--single {
            height: 55px;
            border: 0;
        }

        .section .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 55px;
            height: 55px;
        }

        .section .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 50%;
            right: 10px;
            pointer-events: none;
            transform: translateY(-56%);
        }


        .select-wrapper .select2-container--open {
            width: -webkit-calc(100% - 30px) !important;
            width: calc(100% - 30px) !important;
            position: absolute;
            top: 45px !important;
            left: 15px !important;
            right: 15px !important;
        }

        .select2-container--open .select2-dropdown.select2-dropdown--below {
            width: 100% !important;
            height: 460px;
            left: 0px !important;
            right: 0px !important;
        }
        .select-wrapper .wrapper .select2-container--open .select2-dropdown--below {
            width: auto !important;
            position: absolute;
            top: auto !important;
            left: auto !important;
            right: auto !important;
            height: auto;

        }
        /* .selection,
		.dropdown-wrapper {
			position: absolute;
			left: 0;
			right: 0;
			bottom: 0;
			top: 0;
		} */
        .select-wrapper .select2-dropdown {
            border: 0;
        }

        body {
            margin: 0;
        }

        .select-wrapper .select2-search--dropdown:before {
            content: 'Your Region';
            font-size: 18px;
            font-weight: bold;
            font-style: normal;
            font-stretch: normal;
            line-height: 3.06;
            letter-spacing: normal;
            text-align: left;
            color: #1d1103;
        }

        .select-wrapper .select2-search--dropdown:after {
            content: "\f002";
            font-family: "Font Awesome 5 Pro";
            font-weight: 900;
            -webkit-font-smoothing: antialiased;
            display: inline-block;
            font-style: normal;
            font-variant: normal;
            text-rendering: auto;
            line-height: 1;
            position: absolute;
            top: 90px;
            right: 40px;
        }

        .select-wrapper .select2-search--dropdown {
            padding: 4px 22px;
        }

        .section-select .button-close {
            position: absolute;
            right: 20px;
            top: 50px;
            z-index: 1100;
            font-size: 20px;
            color: #be8a4a;
            visibility: hidden;
        }

        .section-select .select2-container--open + .button-close {
            visibility: visible;
        }

        .section-select .button-close:before {
            content: '\f00d';
            font-family: "Font Awesome 5 Pro";
            font-weight: 900;
            -webkit-font-smoothing: antialiased;
            display: inline-block;
            font-style: normal;
            font-variant: normal;
            text-rendering: auto;
            line-height: 1;
            position: absolute;
            right: 10px;
            top: 10px;
        }

        /* .select-wrapper .select2-hidden-accessible {
			height: 55px !important;
			margin: -1px !important;
			overflow: hidden !important;
			padding: 0 !important;
			position: absolute !important;
			width: calc(100% - 30px) !important;
			left: 0;
		} */

        .select-wrapper .select2-container--default .select2-search--dropdown .select2-search__field {
            height: 55px;
            border-radius: 4px;
            box-shadow: 0 3px 10px 0 rgba(113, 73, 12, 0.31);
            background-color: #ffffff;
            border: 0;
            margin-top: 10px;
            padding: 15px;
        }

        .select2-results {
            padding-top: 75px;
        }

        .select-wrapper .select2-results__option {
            padding: 17px 37px;
            border-bottom: solid 1px #707070;
            font-size: 16px;
            font-weight: normal;
            font-style: normal;
            font-stretch: normal;
            line-height: 1.19;
            letter-spacing: normal;
            text-align: left;
            color: #1d1103;
        }

        .select-wrapper .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color:#be8a4a;
            border-color: #be8a4a;
        }





    </style>
</head>
<body class="select-wrapper">
<div class="section section-select">
    <div class="wrapper">
        <div class="select">
            <h1 class="title-select">Select<br/> Your Region</h1>
            <select class="js-select2" id="type">
                <option>Select A</option>
                <option>Select B</option>
                <option>Select C</option>
                <option>Select D</option>
                <option>Select A</option>
                <option>Select B</option>
                <option>Select C</option>
                <option>Select D</option>
                <option>Select A</option>
                <option>Select B</option>
                <option>Select C</option>
                <option>Select D</option>
                <option>Select A</option>
                <option>Select B</option>
                <option>Select C</option>
                <option>Select D</option>
            </select>
            <div class="button-close"></div>
        </div>
        <div class="clearfix">
            <button type="submit" class="loginbtn">CONTINUE</button>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.full.js"></script>
<script id="rendered-js">
    $(document).ready(function() {
        $(".js-select2").select2({
        });
        $(document).on('click', '.section-select .button-close', function (e) {
            console.log('aaa');
            $(".js-select2").select2("close");
        });
    });
</script>
</body>
</html>