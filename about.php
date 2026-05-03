<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Récupérer la devise choisie
$selectedCurrency = $_COOKIE['selected_currency'] ?? 'USD';

// Fonction helper pour afficher les prix convertis
function displayPrice($priceInUSD) {
    global $selectedCurrency;
    return formatPrice($priceInUSD, $selectedCurrency);
}

// Statistiques (à personnaliser selon tes données réelles)
$stats = [
    'clients' => 12480,
    'destinations' => 45,
    'vols' => 156,
    'hotels' => 320,
    'experience' => 8,
    'satisfaction' => 98
];

// Équipe (à personnaliser)
$team = [
    ['name' => 'Sarah Mbuyi', 'position' => 'Fondatrice & CEO', 'image' => 'https://randomuser.me/api/portraits/women/68.jpg', 'bio' => 'Experte en tourisme africain avec plus de 15 ans d\'expérience'],
    ['name' => 'Jean Kabasele', 'position' => 'Directeur des Opérations', 'image' => 'https://randomuser.me/api/portraits/men/32.jpg', 'bio' => 'Ancien gestionnaire d\'hôtels de luxe à Kinshasa'],
    ['name' => 'Marie Tshibanda', 'position' => 'Responsable Commerciale', 'image' => 'https://randomuser.me/api/portraits/women/45.jpg', 'bio' => 'Passionnée par la découverte des merveilles d\'Afrique'],
    ['name' => 'David Mukendi', 'position' => 'Conseiller Voyages', 'image' => 'https://randomuser.me/api/portraits/men/52.jpg', 'bio' => 'A visité plus de 30 pays africains']
];

// Valeurs de l'entreprise
$values = [
    ['title' => 'Authenticité', 'description' => 'Nous vous proposons des expériences authentiques et locales, loin des circuits touristiques standardisés.', 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    ['title' => 'Excellence', 'description' => 'Un service client irréprochable et une attention particulière à chaque détail de votre voyage.', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
    ['title' => 'Responsabilité', 'description' => 'Engagés pour un tourisme durable et respectueux des communautés locales.', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
    ['title' => 'Innovation', 'description' => 'Une plateforme moderne qui simplifie la réservation de vos voyages en Afrique.', 'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z']
];

// FAQ
$faq = [
    ['question' => 'Comment réserver un voyage ?', 'answer' => 'Vous pouvez réserver directement sur notre site en sélectionnant votre destination, vos dates et le nombre de voyageurs. Notre équipe vous contactera dans les 24h pour confirmer votre réservation.'],
    ['question' => 'Quels sont les moyens de paiement acceptés ?', 'answer' => 'Nous acceptons les cartes bancaires (Visa, Mastercard), les transferts bancaires, et le paiement en espèces dans nos agences à Kinshasa et Lubumbashi.'],
    ['question' => 'Puis-je annuler ma réservation ?', 'answer' => 'Oui, l\'annulation est gratuite jusqu\'à 30 jours avant le départ. Entre 30 et 15 jours, des frais de 25% s\'appliquent. Passé ce délai, aucun remboursement n\'est possible.'],
    ['question' => 'Proposez-vous des vols internationaux ?', 'answer' => 'Oui, nous proposons des vols vers toutes les destinations internationales via nos partenaires aériens (Air France, Brussels Airlines, Ethiopian Airlines, etc.).'],
    ['question' => 'Comment obtenir un visa ?', 'answer' => 'Nous nous occupons des formalités de visa pour la plupart des destinations. Nos conseillers vous guideront dans les démarches nécessaires.'],
    ['question' => 'Y a-t-il des frais cachés ?', 'answer' => 'Non, tous nos prix sont affichés TTC. Ce que vous voyez est ce que vous payez, sans surprise !']
];

// Blog posts récents (à remplacer par des données réelles de ta base)
$blogPosts = [
    ['title' => 'Les plus belles plages de Zanzibar', 'date' => '2026-04-15', 'excerpt' => 'Découvrez les paradis cachés de l\'île aux épices...', 'image' => 'https://images.unsplash.com/photo-1539367628440-4c5f07e3f1b1?w=400'],
    ['title' => 'Guide du voyageur à Kinshasa', 'date' => '2026-04-01', 'excerpt' => 'Tout ce qu\'il faut savoir avant de visiter la capitale congolaise...', 'image' => 'https://images.unsplash.com/photo-1585409677983-0f6c41ca9c3b?w=400'],
    ['title' => 'Safari au Parc des Virunga', 'date' => '2026-03-20', 'excerpt' => 'Une aventure inoubliable à la rencontre des gorilles...', 'image' => 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=400']
];
?>

<style>
    .about-hero {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        position: relative;
        overflow: hidden;
    }
    .about-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('https://images.unsplash.com/photo-1523805009345-7448845a9e53?w=1920') center/cover;
        opacity: 0.15;
    }
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
    }
    .value-card {
        transition: all 0.3s ease;
    }
    .value-card:hover {
        transform: translateY(-4px);
    }
    .team-card {
        transition: all 0.3s ease;
    }
    .team-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
    }
    .faq-question {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .faq-question:hover {
        background-color: #f3f4f6;
    }
    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }
    .faq-answer.open {
        max-height: 300px;
    }
</style>

<!-- Hero Section -->
<section class="about-hero pt-32 pb-20 relative">
    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-3xl mx-auto text-center text-white">
            <h1 class="text-5xl md:text-6xl font-bold mb-6 animate-fadeInUp">À propos de Luvia Travel</h1>
            <p class="text-xl md:text-2xl mb-8 opacity-90 animate-fadeInUp animation-delay-200">
            Votre partenaire de confiance pour découvrir l'Afrique autrement
            </p>
            <div class="flex flex-wrap justify-center gap-4 animate-fadeInUp animation-delay-400">
                <a href="#contact" class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-3 rounded-full font-semibold transition shadow-lg">Nous contacter</a>
                <a href="#values" class="border-2 border-white text-white hover:bg-white hover:text-blue-600 px-8 py-3 rounded-full font-semibold transition">Nos valeurs</a>
            </div>
        </div>
    </div>
</section>

<!-- Story Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="order-2 lg:order-1">
                <span class="text-blue-600 font-semibold text-sm uppercase tracking-wide">Notre histoire</span>
                <h2 class="text-4xl font-bold text-gray-800 mt-2 mb-4">Née d'une passion pour l'Afrique</h2>
                <div class="w-20 h-1 bg-blue-600 mb-6"></div>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    Fondée en 2018 par des passionnés de voyages et d'aventure, Luvia Travel est née d'une conviction : 
                    l'Afrique regorge de trésors naturels et culturels qui méritent d'être découverts dans les meilleures conditions.
                </p>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    Ce qui a commencé comme une petite agence à Kinshasa est rapidement devenu une référence du tourisme 
                    en République Démocratique du Congo et en Afrique centrale. Aujourd'hui, nous accompagnons plus de 
                    <span class="font-bold text-blue-600">12 000 voyageurs</span> chaque année vers des destinations 
                    exceptionnelles.
                </p>
                <p class="text-gray-600 leading-relaxed">
                    Notre mission : vous offrir des voyages sur mesure, alliant confort, authenticité et découverte, 
                    tout en soutenant les communautés locales et en préservant l'environnement.
                </p>
            </div>
            <div class="order-1 lg:order-2">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1523805009345-7448845a9e53?w=800" alt="Équipe Luvia Travel" class="w-full h-[400px] object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent flex items-end">
                        <div class="p-6 text-white">
                            <p class="text-sm">L'équipe Luvia Travel - Kinshasa</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-16 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 text-center">
            <div class="stat-card bg-white/10 rounded-2xl p-6 backdrop-blur-sm">
                <div class="text-4xl font-bold"><?php echo number_format($stats['clients']); ?>+</div>
                <div class="text-sm opacity-90 mt-2">Clients satisfaits</div>
            </div>
            <div class="stat-card bg-white/10 rounded-2xl p-6 backdrop-blur-sm">
                <div class="text-4xl font-bold"><?php echo $stats['destinations']; ?></div>
                <div class="text-sm opacity-90 mt-2">Destinations</div>
            </div>
            <div class="stat-card bg-white/10 rounded-2xl p-6 backdrop-blur-sm">
                <div class="text-4xl font-bold"><?php echo $stats['vols']; ?>+</div>
                <div class="text-sm opacity-90 mt-2">Vols par semaine</div>
            </div>
            <div class="stat-card bg-white/10 rounded-2xl p-6 backdrop-blur-sm">
                <div class="text-4xl font-bold"><?php echo $stats['hotels']; ?></div>
                <div class="text-sm opacity-90 mt-2">Hôtels partenaires</div>
            </div>
            <div class="stat-card bg-white/10 rounded-2xl p-6 backdrop-blur-sm">
                <div class="text-4xl font-bold"><?php echo $stats['satisfaction']; ?>%</div>
                <div class="text-sm opacity-90 mt-2">Taux de satisfaction</div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section id="values" class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <span class="text-blue-600 font-semibold text-sm uppercase tracking-wide">Pourquoi nous choisir</span>
            <h2 class="text-4xl font-bold text-gray-800 mt-2">Nos valeurs fondamentales</h2>
            <div class="w-20 h-1 bg-blue-600 mx-auto mt-4"></div>
            <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Des principes qui guident chacune de nos actions</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach($values as $value): ?>
            <div class="value-card bg-white rounded-2xl p-6 shadow-lg text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?php echo $value['icon']; ?>"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo $value['title']; ?></h3>
                <p class="text-gray-600 text-sm"><?php echo $value['description']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <span class="text-blue-600 font-semibold text-sm uppercase tracking-wide">Notre équipe</span>
            <h2 class="text-4xl font-bold text-gray-800 mt-2">Des experts passionnés</h2>
            <div class="w-20 h-1 bg-blue-600 mx-auto mt-4"></div>
            <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Une équipe locale dédiée à faire de votre voyage une expérience unique</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach($team as $member): ?>
            <div class="team-card bg-gray-50 rounded-2xl overflow-hidden shadow-lg">
                <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" class="w-full h-64 object-cover">
                <div class="p-6 text-center">
                    <h3 class="text-lg font-bold text-gray-800"><?php echo $member['name']; ?></h3>
                    <p class="text-blue-600 text-sm mb-2"><?php echo $member['position']; ?></p>
                    <p class="text-gray-500 text-sm"><?php echo $member['bio']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-20 bg-blue-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <span class="text-blue-600 font-semibold text-sm uppercase tracking-wide">Témoignages</span>
            <h2 class="text-4xl font-bold text-gray-800 mt-2">Ce que disent nos clients</h2>
            <div class="w-20 h-1 bg-blue-600 mx-auto mt-4"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <div class="flex items-center gap-1 mb-4">
                    <?php for($i=0;$i<5;$i++): ?>
                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <?php endfor; ?>
                </div>
                <p class="text-gray-600 mb-4">"Un service exceptionnel ! L'équipe a été très professionnelle et à l'écoute. Mon voyage à Zanzibar était parfaitement organisé."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-300 rounded-full overflow-hidden">
                        <img src="https://randomuser.me/api/portraits/women/33.jpg" alt="Client" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Marie-Claire</p>
                        <p class="text-xs text-gray-500">Voyage à Zanzibar</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <div class="flex items-center gap-1 mb-4">
                    <?php for($i=0;$i<5;$i++): ?>
                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <?php endfor; ?>
                </div>
                <p class="text-gray-600 mb-4">"Je recommande vivement Luvia Travel. Les prix sont compétitifs et le suivi client est impeccable. Parfait pour un voyage organisé."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-300 rounded-full overflow-hidden">
                        <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Client" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Jean-Pierre</p>
                        <p class="text-xs text-gray-500">Voyage à Dubai</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <div class="flex items-center gap-1 mb-4">
                    <?php for($i=0;$i<5;$i++): ?>
                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <?php endfor; ?>
                </div>
                <p class="text-gray-600 mb-4">"Une agence de confiance ! J'ai réservé un package pour Le Caire et tout était parfait : vols, hôtel, excursions. Merci !"</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-300 rounded-full overflow-hidden">
                        <img src="https://randomuser.me/api/portraits/women/22.jpg" alt="Client" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Aline</p>
                        <p class="text-xs text-gray-500">Voyage au Caire</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <span class="text-blue-600 font-semibold text-sm uppercase tracking-wide">Questions fréquentes</span>
            <h2 class="text-4xl font-bold text-gray-800 mt-2">Tout ce que vous devez savoir</h2>
            <div class="w-20 h-1 bg-blue-600 mx-auto mt-4"></div>
        </div>
        
        <div class="max-w-3xl mx-auto space-y-4">
            <?php foreach($faq as $index => $item): ?>
            <div class="bg-gray-50 rounded-xl overflow-hidden">
                <div class="faq-question p-5 flex justify-between items-center" data-faq="<?php echo $index; ?>">
                    <h3 class="font-semibold text-gray-800"><?php echo $item['question']; ?></h3>
                    <svg class="w-5 h-5 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
                <div class="faq-answer" id="faq-answer-<?php echo $index; ?>">
                    <div class="p-5 pt-0 text-gray-600 border-t border-gray-200">
                        <?php echo $item['answer']; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section id="contact" class="py-20 bg-gradient-to-r from-blue-600 to-blue-800">
    <div class="container mx-auto px-6 text-center text-white">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Prêt à vivre une aventure inoubliable ?</h2>
        <p class="text-lg mb-8 opacity-90 max-w-2xl mx-auto">Contactez-nous dès maintenant pour commencer à planifier votre prochain voyage</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="contact.php" class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-3 rounded-full font-semibold transition shadow-lg">
                Nous contacter
            </a>
            <a href="index.php" class="border-2 border-white text-white hover:bg-white hover:text-blue-600 px-8 py-3 rounded-full font-semibold transition">
                Voir nos offres
            </a>
        </div>
    </div>
</section>

<script>
// Animation FAQ
document.querySelectorAll('.faq-question').forEach(btn => {
    btn.addEventListener('click', () => {
        const index = btn.dataset.faq;
        const answer = document.getElementById(`faq-answer-${index}`);
        const svg = btn.querySelector('svg');
        
        answer.classList.toggle('open');
        svg.style.transform = answer.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0deg)';
    });
});

// Animation au scroll
const animateElements = document.querySelectorAll('.stat-card, .value-card, .team-card');
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if(entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, { threshold: 0.1 });

animateElements.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'all 0.5s ease';
    observer.observe(el);
});
</script>

<?php require_once 'includes/footer.php'; ?>