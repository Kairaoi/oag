<style>
    header {
        background: linear-gradient(135deg, #ffeb3b, #ff9800);
        color: #fff;
        text-align: center;
        padding: 18px 20px;
        position: relative;
        border-bottom: 4px solid #ff9800;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    header img {
        max-height: 56px;
        margin-bottom: 8px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    header h1 {
        font-size: 26px;
        font-weight: bold;
        margin-bottom: 4px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    header p {
        font-size: 15px;
        margin-top: 0;
        margin-bottom: 8px;
    }

    .header-decorative {
        background: linear-gradient(90deg, #ffeb3b, #ff9800);
        height: 5px;
        margin-top: 8px;
        border-radius: 5px;
    }
</style>

<header>
    <div class="container">
        <img src="{{ asset('images/oag_logo.png') }}" alt="Office Logo">
        <h1>Office of the Attorney General</h1>
        <p class="header-description">Ensuring Justice and Legal Integrity</p>
        <div class="header-decorative"></div>
    </div>
</header>
