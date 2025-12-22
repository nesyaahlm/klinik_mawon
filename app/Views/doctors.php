<?= $this->include('layout/header') ?>

<style>
:root {
    --primary: #2E86DE;
    --secondary: #1F4F7B;
    --accent: #FFD166;
    --card-bg: #ffffff;
    --card-shadow: rgba(46, 134, 222, 0.2);
    --text: #1f2937;
    --gray: #6b7280;
}

body {
    font-family: 'Open Sans', sans-serif;
    background: linear-gradient(135deg, #E9F5FF 0%, #ffffff 100%);
    position: relative;
    margin: 0;
    padding: 0;
    color: var(--text);
}

body::before {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at top left, rgba(46,134,222,0.08), transparent 70%),
                radial-gradient(circle at bottom right, rgba(255,209,102,0.05), transparent 60%);
    pointer-events: none;
}

h5 {
    font-family: 'Poppins', sans-serif;
    font-size: 1.6rem;
    font-weight: 600;
    text-align: center;
    color: var(--primary);
    margin: 40px 0 20px;
}
h5::after {
    content: '';
    display: block;
    width: 70px;
    height: 3px;
    background: var(--primary);
    margin: 8px auto 0;
    border-radius: 2px;
}

.doctor-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 20px;
    max-width: 1000px;
    margin: 0 auto 50px;
    padding: 0 10px;
    position: relative;
    z-index: 1;
}

.doctor-card {
    position: relative;
    background: var(--card-bg);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 6px 15px var(--card-shadow);
    text-decoration: none;
    color: inherit;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
    display: flex;
    flex-direction: column;
    animation: fadeInUp 0.8s ease forwards;
}
.doctor-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 30px rgba(46,134,222,0.25);
    background: #f0f8ff;
}

.doctor-card .doctor-img-wrapper {
    width: 100%;
    padding-top: 110%; 
    position: relative;
    overflow: hidden;
    background: #f8f8f8;
}
.doctor-card .doctor-img-wrapper img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease, filter 0.3s ease;
}
.doctor-card:hover .doctor-img-wrapper img {
    transform: scale(1.05);
    filter: brightness(1.05);
}

.doctor-info {
    padding: 10px 6px;
    text-align: center;
}
.doctor-card h3 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--secondary);
    margin: 6px 0 2px;
}
.doctor-card small {
    font-size: 0.75rem;
    color: var(--gray);
    display: block;
    margin-bottom: 4px;
}

.doctor-rating {
    margin-bottom: 6px;
    color: var(--accent);
    font-size: 0.75rem;
}
.star {
    display: inline-block;
    width: 12px;
    height: 12px;
    color: var(--accent);
}

.btn-book {
    display: inline-block;
    margin-top: 4px;
    padding: 5px 12px;
    color: #fff;
    background: linear-gradient(45deg, #2E86DE, #1F4F7B);
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.75rem;
    text-decoration: none;
    transition: 0.3s ease, box-shadow 0.3s ease;
}
.btn-book:hover {
    background: var(--accent);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(46,134,222,0.3);
}

@keyframes fadeInUp {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}


</style>

<section>
    <h5>Tim Dokter Klinik Mawon</h5>

    <div class="doctor-grid">
        <?php 
        $ratings = [5, 4, 4.5]; 
        $i = 0;
        foreach($doctors as $doc): 
            $rating = $ratings[$i % count($ratings)];
        ?>
        <div class="doctor-card">
            <div class="doctor-img-wrapper">
                <?php if(!empty($doc['photo'])): ?>
                    <img src="<?= base_url('img/'.$doc['photo']) ?>" alt="<?= esc($doc['name']) ?>">
                <?php else: ?>
                    <img src="<?= base_url('img/default-doctor.jpg') ?>" alt="Default Doctor">
                <?php endif; ?>
            </div>

            <div class="doctor-info">
                <h3><?= esc($doc['name']) ?></h3>
                <div class="doctor-rating">
                    <?php
                    $fullStars = floor($rating);
                    $halfStar = ($rating - $fullStars) >= 0.5 ? true : false;
                    for($s=0;$s<$fullStars;$s++): ?>
                        <span class="star">★</span>
                    <?php endfor; ?>
                    <?php if($halfStar): ?>
                        <span class="star">☆</span>
                    <?php endif; ?>
                    <?php for($s=0;$s<5-ceil($rating);$s++): ?>
                        <span class="star">☆</span>
                    <?php endfor; ?>
                </div>
                <small><?= esc($doc['schedule'] ?? 'Senin - Jumat: 08:00 - 15:00') ?></small>
                <a href="<?= base_url('schedule/'.$doc['id']) ?>" class="btn-book">Booking</a>
            </div>
        </div>
        <?php 
        $i++;
        endforeach; 
        ?>
    </div>
</section>

<?= $this->include('layout/footer') ?>
