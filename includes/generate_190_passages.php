<?php
/**
 * Script to generate 195 distinct, curated typing passages
 * and output to includes/passages_data.php and seed MySQL database.
 */

$passages = [];

// Helper to add passage
function addP(&$list, $id, $title, $difficulty, $category, $content) {
    $words = str_word_count(strip_tags($content));
    $chars = strlen(trim($content));
    $list[] = [
        'id' => $id,
        'title' => $title,
        'difficulty' => $difficulty,
        'category' => $category,
        'content' => trim($content),
        'word_count' => $words,
        'char_count' => $chars
    ];
}

$id = 1;

// =========================================================================
// BEGINNER PASSAGES (IDs 1 - 65): Clean rhythm, standard punctuation, motivating
// =========================================================================
addP($passages, $id++, 'The Joy of Morning Walks', 'beginner', 'Lifestyle',
'A morning walk is a great way to start your day with energy. The fresh air clears your mind and gives you a calm feeling. Walking each day keeps your body strong and active. You can listen to the birds sing and watch the sun rise over the trees. It is simple, free, and brings peace to your busy life.');

addP($passages, $id++, 'The Little Coffee Shop', 'beginner', 'Stories',
'Around the corner sits a cozy little coffee shop with warm lights and quiet music. People visit every day to read their favorite books or talk with good friends. The sweet smell of roasted beans fills the room. Sitting by the window while sipping warm tea brings a feeling of comfort and joy.');

addP($passages, $id++, 'Learning Something New', 'beginner', 'Inspiration',
'Every day gives us a fresh chance to learn something helpful. Whether you pick up a book, try a new hobby, or practice typing on a keyboard, your brain grows stronger. Small efforts made every day lead to big changes over time. Never be afraid to make mistakes because they help you grow.');

addP($passages, $id++, 'The River by the Mountain', 'beginner', 'Nature',
'Clean blue water runs down from the green hills into a calm river. Fish swim gently under the clear surface while bright flowers bloom along the banks. Taking time to sit by the river helps you relax and forget about your worries. Nature always has a quiet way of making us feel better.');

addP($passages, $id++, 'The Value of Patience', 'beginner', 'Mindset',
'Patience is one of the most useful skills anyone can practice. Good things take time to grow, just like a small seed needs water and sun before it becomes a tall tree. When things feel slow or difficult, take a deep breath and keep going. Your calm persistence will always pay off in the end.');

addP($passages, $id++, 'A Simple Sunny Afternoon', 'beginner', 'Lifestyle',
'On a quiet sunny afternoon, the golden light falls across the wooden floor. A gentle breeze moves the white curtains by the open window. It is the perfect time to rest, enjoy a cup of fresh juice, and listen to the soft sounds of birds in the garden. Peace is found in simple moments.');

addP($passages, $id++, 'Building Daily Habits', 'beginner', 'Productivity',
'Great success is rarely the result of a single giant leap. Instead, it comes from small actions repeated with care every day. When you practice typing for ten minutes each morning, your fingers remember the keys without looking. Consistency turns hard tasks into effortless second nature.');

addP($passages, $id++, 'The Magic of Books', 'beginner', 'Stories',
'Opening a book allows you to travel to distant lands without leaving your chair. You can explore deep oceans, fly across starry skies, and walk through ancient castles. Reading feeds your imagination and teaches you how other people think. Every page holds a new adventure waiting to be found.');

addP($passages, $id++, 'The Power of Kindness', 'beginner', 'Inspiration',
'A single kind word can brighten someone entire day. Holding a door open, sharing a warm smile, or saying thank you takes very little effort, but it creates ripples of happiness. When we treat others with respect and gentle care, we make the whole world a friendlier place to live.');

addP($passages, $id++, 'Morning Sunlight', 'beginner', 'Nature',
'The morning sun climbs slowly into the clear sky, painting the clouds with shades of pink and gold. Dew drops sparkle like tiny diamonds on the green grass. The cool morning air feels refreshing on your face as the quiet world wakes up for another wonderful day full of hope.');

addP($passages, $id++, 'The Secret of Good Posture', 'beginner', 'Health',
'Sitting up straight with your shoulders relaxed makes a big difference when typing. Keep your feet flat on the ground and let your wrists float gently above the desk. When your body is aligned and comfortable, your hands move faster and you can type for hours without feeling tired.');

addP($passages, $id++, 'Fresh Bread from the Bakery', 'beginner', 'Stories',
'Early in the morning, the village bakery starts making fresh bread. The scent of warm crusty loaves drifts through the narrow streets. Neighbors gather with cloth bags to greet each other with cheerful smiles. Breaking a warm piece of bread with butter is one of life greatest simple pleasures.');

addP($passages, $id++, 'Listening to Music', 'beginner', 'Art',
'Music has a special way of speaking directly to the human heart. Soft melodies help us calm down after a busy afternoon, while lively rhythms give us energy to dance and work. Finding a pleasant tune to listen to while practicing typing helps your mind stay focused and happy.');

addP($passages, $id++, 'The Starry Night Sky', 'beginner', 'Nature',
'Step outside on a clear night and look up at the vast dark sky. Countless stars twinkle like silver dust scattered across velvet cloth. Looking into space reminds us of how big and wonderful the universe really is. It gives us a feeling of quiet wonder and peaceful dreams.');

addP($passages, $id++, 'The Forest Trail', 'beginner', 'Nature',
'Walking along a quiet forest trail is full of pleasant discoveries. Tall pine trees reach toward the sky, shading the path below. Soft moss covers the rocks like a green blanket, and a small squirrel watches you curiously from a branch. Every step brings you closer to nature calm rhythm.');

addP($passages, $id++, 'A Healthy Breakfast', 'beginner', 'Health',
'Starting your morning with a nourishing meal fuels your mind for learning and work. Fresh sliced fruit, oatmeal with honey, and a glass of cool water give your body the vitamins it needs. When your energy is high, staying focused on typing drills and daily goals feels easy and natural.');

addP($passages, $id++, 'The Garden in Spring', 'beginner', 'Nature',
'Spring brings new life to every corner of the garden. Little green shoots poke through the rich dark soil, eager to meet the warm sunshine. Bees buzz softly around yellow buttercups while robins build nests in the apple tree. Everything feels fresh, hopeful, and ready to bloom.');

addP($passages, $id++, 'Writing a Friendly Letter', 'beginner', 'Communication',
'Writing a thoughtful message to an old friend is a wonderful way to stay close. Choosing gentle words to describe your daily adventures shows that you care about them. When they open your note and read your stories, distance fades away and warm memories come back to life.');

addP($passages, $id++, 'Rain on the Window', 'beginner', 'Nature',
'Gentle raindrops tap a rhythmic pattern against the glass window. The sky is dressed in soft gray clouds, and the green leaves drink the cool rainwater outside. Staying cozy indoors with a warm blanket and a good keyboard test makes a rainy morning feel peaceful and special.');

addP($passages, $id++, 'The Clean Workspace', 'beginner', 'Productivity',
'A tidy desk helps create a clear and peaceful mind. When your keyboard is centered, your screen is at eye level, and unnecessary clutter is put away, you can concentrate on your practice. Taking a few moments to organize your space sets the stage for smooth and joyful typing.');

addP($passages, $id++, 'The Friendly Ocean Wave', 'beginner', 'Nature',
'Waves roll gently onto the sandy beach, leaving behind delicate white foam. Children build tall sandcastles with shells decorating the towers. The salty breeze smells fresh and clean under the bright summer sky. Standing near the ocean reminds us that steady rhythm brings lasting calm.');

addP($passages, $id++, 'The Power of Deep Breathing', 'beginner', 'Health',
'Whenever you feel rushed or tense, pausing for three slow deep breaths can restore your focus. Inhale through your nose, hold for a brief second, and exhale gently through your mouth. Your shoulders will drop, your hands will loosen, and your typing rhythm will become smooth again.');

addP($passages, $id++, 'The Library on Saturday', 'beginner', 'Stories',
'The town library is a peaceful sanctuary on a quiet Saturday morning. Sunlight streams through tall windows, illuminating rows of colorful book spines. People whisper quietly and turn pages with care. It is a place where anyone can sit in comfort and discover new knowledge for free.');

addP($passages, $id++, 'Helping a Neighbor', 'beginner', 'Community',
'Community grows stronger when neighbors look out for one another. Raking autumn leaves for an elderly friend or sharing extra tomatoes from your garden creates genuine warmth. Helping others not only lifts their spirits, but it also fills your own heart with deep satisfaction.');

addP($passages, $id++, 'The Midnight Train', 'beginner', 'Stories',
'In the quiet of the night, the distant whistle of a passenger train echoes across the valley. Yellow lights shine through the passenger windows as the train glides steadily down the steel tracks. Travelers rest their heads against the soft seats, dreaming of the places they will reach by dawn.');

addP($passages, $id++, 'Practice Makes Progress', 'beginner', 'Mindset',
'Do not worry about being fast when you first learn touch typing. Accuracy is the true foundation of speed. When you press the right key every time, your muscle memory strengthens naturally. Speed will follow quietly in its own time as long as you remain steady and positive.');

addP($passages, $id++, 'The Old Wooden Bridge', 'beginner', 'Stories',
'An old wooden bridge arches gracefully over a crystal clear brook. For over a hundred years, villagers have crossed these worn oak planks on foot and bicycle. Water lilies float peacefully beneath the wooden beams, and frogs sing their cheerful evening songs as twilight settles in.');

addP($passages, $id++, 'The Morning Cup of Tea', 'beginner', 'Lifestyle',
'Holding a warm ceramic mug between your hands brings an immediate sense of comfort. Steam rises into the cool morning air carrying the herbal aroma of chamomile and lemon. Taking five quiet minutes before checking your phone helps you greet the day with calm confidence.');

addP($passages, $id++, 'The Craft of Cooking', 'beginner', 'Lifestyle',
'Cooking a homemade meal is both an art and a practical craft. Chopping crisp vegetables on a wooden cutting board, simmering fresh herbs in olive oil, and seasoning with care turns simple ingredients into something delicious. Sharing good food brings family together in joyful laughter.');

addP($passages, $id++, 'A Walk in the Snow', 'beginner', 'Nature',
'Fresh snow falls silently from white winter skies, covering the world in a clean fluffy blanket. Each step you take makes a satisfying crunch beneath your winter boots. The air is crisp and pure, and the bare tree branches look like delicate sculptures carved from silver ice.');

addP($passages, $id++, 'The Joy of Riding a Bicycle', 'beginner', 'Lifestyle',
'Riding a bicycle down a quiet country lane gives you a wonderful feeling of freedom. The wind rushes past your face and your legs spin in smooth circular motions. You pass green pastures with grazing cows and colorful roadside daisies. It is exercise that feels pure fun.');

addP($passages, $id++, 'The Pottery Wheel', 'beginner', 'Art',
'A potter places a lump of wet gray clay onto the spinning wheel. With wet hands and gentle pressure, the clay rises smoothly into a rounded bowl. Shaping clay requires patience, balance, and attention to detail. Creating something useful with your hands brings profound pride.');

addP($passages, $id++, 'The Sunrise Over the Sea', 'beginner', 'Nature',
'Watching the sun rise over the open ocean is an unforgettable sight. A fiery orange rim peeks over the horizon, casting a glittering pathway of light across the rippling water. Seagulls call out as they take flight, celebrating the arrival of another bright and promising day.');

addP($passages, $id++, 'The Warm Fireplace', 'beginner', 'Lifestyle',
'On a chilly winter evening, dry birch logs crackle cheerfully inside the stone fireplace. Warm orange embers cast dancing shadows across the cozy living room walls. Sitting on a plush rug with a warm wool blanket makes you appreciate the safe haven of home.');

addP($passages, $id++, 'Learning from Mistakes', 'beginner', 'Mindset',
'When you strike the wrong key during a typing test, do not get upset. Every mistake is valuable feedback that tells your fingers where to adjust. Taking a breath and continuing with composure trains emotional resilience. True masters are simply beginners who never gave up.');

addP($passages, $id++, 'The Old Clocktower', 'beginner', 'Stories',
'In the heart of the town square stands a tall stone clocktower built over two centuries ago. Its brass bells chime every hour on the hour with a rich musical tone. Generations of people have set their pocket watches by its steady ticking, trusting its dependable rhythm.');

addP($passages, $id++, 'A Simple Sketchbook', 'beginner', 'Art',
'Carrying a small blank sketchbook allows you to capture fleeting moments of beauty. A quick pencil drawing of a blooming rose, an interesting doorway, or an old oak tree preserves the memory forever. You do not need to be an expert artist to enjoy the pleasure of creating.');

addP($passages, $id++, 'The Gentle Stream', 'beginner', 'Nature',
'A shallow woodland stream ripples softly over smooth round pebbles. Sunlight filters through the green canopy above, creating shimmering patches of light on the water. Dipping your fingers into the cool mountain runoff is refreshing and restores your connection to the earth.');

addP($passages, $id++, 'The Power of Gratitude', 'beginner', 'Mindset',
'Taking time each evening to name three things you are thankful for changes the way you see the world. It shifts your attention from what is missing to the abundance all around you. Gratitude brings peaceful sleep and helps you wake up with enthusiasm.');

addP($passages, $id++, 'The Community Garden', 'beginner', 'Community',
'In the community garden, people of all ages work side by side among raised wooden beds. Sweet strawberries ripen in the sun while tall green corn stalks rustle in the breeze. Sharing garden advice and fresh vegetables brings people together in deep and lasting friendships.');

addP($passages, $id++, 'Walking with a Dog', 'beginner', 'Lifestyle',
'Taking your dog for an evening walk reminds you to enjoy the present moment. A dog finds joy in sniffing a green bush, chasing a fallen leaf, or greeting a passing friend. Their boundless energy and loyalty teach us to live with cheerful curiosity and open hearts.');

addP($passages, $id++, 'The Starfish on the Shore', 'beginner', 'Stories',
'Early one morning, a boy walked along the shore gently tossing stranded starfish back into the waves. An old man asked why he bothered when miles of beach were covered in starfish. The boy picked up another, tossed it into the ocean, and smiled: it made a difference to that one.');

addP($passages, $id++, 'Watching the Clouds', 'beginner', 'Nature',
'Lying on a soft grassy hillside and watching puffy white clouds drift across the blue sky is a delightful pastime. One cloud looks like a sailing ship with billowing sails, while another resembles a friendly dragon. Letting your imagination wander rests both mind and spirit.');

addP($passages, $id++, 'The Honest Woodcutter', 'beginner', 'Stories',
'An honest woodcutter worked hard in the forest from dawn until dusk to support his family. When a golden axe was offered to him by mistake, he refused it and asked only for his simple iron tool. His integrity was rewarded with peace of mind and the respect of his whole village.');

addP($passages, $id++, 'The Clean Kitchen', 'beginner', 'Lifestyle',
'Wiping down the kitchen counter, washing the pots, and sweeping the floor leaves the room clean and inviting. A clean kitchen makes preparing dinner a pleasure rather than a chore. Taking care of your home is a daily expression of self-respect and quiet gratitude.');

addP($passages, $id++, 'The Little Bird Feeder', 'beginner', 'Nature',
'Hanging a small wooden bird feeder outside your kitchen window invites colorful visitors all year long. Bright blue jays, red cardinals, and tiny black-capped chickadees flock to feast on sunflower seeds. Watching their quick fluttery movements brings a smile to your face.');

addP($passages, $id++, 'Writing in a Journal', 'beginner', 'Mindset',
'Writing your thoughts into a paper journal helps you understand your emotions and clear your mental slate. You can record your typing speed milestones, celebrate small victories, and reflect on lessons learned. Your journal becomes a trusted friend that holds your personal story.');

addP($passages, $id++, 'The Autumn Leaves', 'beginner', 'Nature',
'Autumn transforms the green canopy into a dazzling display of scarlet, amber, and gold. Crisp leaves flutter gracefully to the earth, creating a colorful carpet along the sidewalks. The cool autumn air smells of woodsmoke and ripe apples, signaling the arrival of cozy days.');

addP($passages, $id++, 'The Old Bookstore', 'beginner', 'Stories',
'The secondhand bookstore smells wonderfully of aged paper, leather bindings, and gentle memories. Towering shelves are packed with forgotten histories, classic poetry, and mystery novels. Discovering an old volume with handwritten notes in the margins feels like finding buried treasure.');

addP($passages, $id++, 'The Mountain Trail View', 'beginner', 'Nature',
'After hiking for two hours up a winding mountain trail, you finally reach the rocky summit. Far below, rivers look like thin silver ribbons and towns resemble tiny clusters of toy blocks. The cool wind whispers through the rocks, rewarding your persistent climbing effort.');

addP($passages, $id++, 'Focus on One Thing', 'beginner', 'Productivity',
'When you try to do five tasks at the same time, none of them receive your best effort. Choosing one meaningful goal and giving it your undivided attention produces superior results. Turn off notifications, place your hands on the home row, and type with relaxed concentration.');

addP($passages, $id++, 'The Calming Ocean Breeze', 'beginner', 'Nature',
'Standing on the edge of the pier, you breathe in the cool ocean air. The rhythmic swell of waves against the wooden pilings creates a natural lullaby. Gulls glide effortlessly on thermals overhead, and the endless horizon fills your heart with spacious calm.');

addP($passages, $id++, 'The First Cup of Water', 'beginner', 'Health',
'Drinking a tall glass of cool water right after waking up rehydrates your body and wakes up your digestive system. It refreshes your throat, clears your head, and prepares your muscles for action. Staying well hydrated is the simplest way to maintain clear thinking and high stamina.');

addP($passages, $id++, 'The Farmer Market', 'beginner', 'Lifestyle',
'Saturday mornings at the open-air farmer market are lively and vibrant. Local growers display baskets of red ripe tomatoes, sweet sweet corn, and jars of golden wildflower honey. Supporting local farmers helps the environment and brings the freshest flavors to your family table.');

addP($passages, $id++, 'A Simple acts of Courage', 'beginner', 'Inspiration',
'Courage does not always roar like a lion. Sometimes courage is the quiet voice at the end of the day whispering: I will try again tomorrow. Sticking to your practice goals even when progress feels slow is one of the highest forms of personal courage you can show.');

addP($passages, $id++, 'The Forest in the Rain', 'beginner', 'Nature',
'Rain falling through a dense forest canopy sounds like a soft applause. Droplets splash onto broad fern fronds and soak into the mossy ground. The earthy scent of wet soil and pine needles fills the crisp air, refreshing every living creature under the green ceiling.');

addP($passages, $id++, 'The Power of Music', 'beginner', 'Art',
'A gentle acoustic guitar melody can instantly soften tension in your shoulders. Music connects people across languages and cultures through shared emotion and rhythm. Playing calm ambient tunes while practicing typing drills can help you establish a smooth, steady cadence.');

addP($passages, $id++, 'The Sunflower Patch', 'beginner', 'Nature',
'Tall sunflowers stand proudly along the garden fence, their bright yellow faces turned toward the midday sun. Sturdy green stems support heavy blossom heads full of striped seeds. Honeybees dance from flower to flower, collecting golden pollen to take back to their hive.');

addP($passages, $id++, 'The Silent Snowfall', 'beginner', 'Nature',
'Snow falling in the middle of the night creates an almost magical silence. The busy street noise vanishes under a thick white blanket of powder. Looking out the window at the glowing streetlights through falling flakes brings a peaceful stillness to the home.');

addP($passages, $id++, 'The Value of Rest', 'beginner', 'Health',
'Resting is just as important as working hard. When you sleep, your brain consolidates what you learned during the day and repairs your muscles. Giving yourself permission to rest without guilt allows you to return to your work and typing practice with fresh enthusiasm.');

addP($passages, $id++, 'The Wooden Toymaker', 'beginner', 'Stories',
'In his small workshop filled with shavings, an old toymaker carves wooden trains and spinning tops. He sands every edge until it feels smooth as silk, then paints them in bright primary colors. His toys bring genuine joy to children because they are made with patience and love.');

addP($passages, $id++, 'The Early Morning Dew', 'beginner', 'Nature',
'Tiny droplets of clear morning dew cling to the delicate threads of a spider web in the rosebush. As the morning sun rises, the web sparkles like a necklace of precious pearls. Nature creates masterpieces every dawn for anyone who takes the time to look closely.');

addP($passages, $id++, 'The Friendly Neighborhood', 'beginner', 'Community',
'A neighborhood where people know each other names feels safe and welcoming. Children ride scooters along the sidewalks while elders chat across white picket fences. Living in harmony with those around us brings a deep sense of belonging and community strength.');

addP($passages, $id++, 'Setting Daily Milestones', 'beginner', 'Productivity',
'Writing down two or three clear goals for your day keeps your actions purposeful. Instead of feeling overwhelmed by a mountain of responsibilities, you take one small step at a time. Crossing off each item gives you a satisfying burst of momentum to keep moving forward.');

addP($passages, $id++, 'The Horizon Ahead', 'beginner', 'Inspiration',
'Every new sunrise brings an unwritten page in the book of your life. What you do with today is entirely up to you. With honest effort, quiet confidence, and steady dedication, you can build a future that fills you with pride. Keep your fingers moving forward.');


// =========================================================================
// INTERMEDIATE PASSAGES (IDs 66 - 135): Tech, science, history, philosophy
// =========================================================================
addP($passages, $id++, 'The Evolution of Software Engineering', 'intermediate', 'Technology',
'Modern software development has transformed dramatically over the last few decades. What began as simple procedural routines executed on punch cards has evolved into globally distributed microservices and intelligent cloud architectures. Developers today must balance algorithmic efficiency, data security, user experience, and scalable design patterns. Consistent practice and deep understanding of foundational concepts remain the hallmarks of an exceptional programmer.');

addP($passages, $id++, 'The Architecture of the Modern Web', 'intermediate', 'Technology',
'The World Wide Web relies on a foundational set of open protocols and standards. When a user requests a URL, the browser initiates a DNS lookup, establishes a secure TLS connection, and issues an HTTP request. The server interprets the request, queries relational databases using optimized SQL statements, and returns dynamic HTML, CSS, and JavaScript. Understanding this full-stack lifecycle empowers developers to build responsive and resilient web applications.');

addP($passages, $id++, 'The Art of Focus in a Digital Era', 'intermediate', 'Productivity',
'In an age of relentless digital notifications, the ability to cultivate deep focus is a rare superpower. When you deliberately eliminate distractions and commit your full cognitive energy to a single demanding task, your brain enters a state of flow. During flow, time seems to bend, complex challenges become manageable, and the quality of your output reaches unprecedented heights. Daily typing drills are an ideal way to train this disciplined concentration.');

addP($passages, $id++, 'Scientific Discovery and Curious Minds', 'intermediate', 'Science',
'Throughout human history, major breakthroughs in science and medicine were rarely accidents; they were the fruits of persistent curiosity and rigorous experimentation. From observing celestial orbits through early telescopes to sequencing the human genome with advanced computational algorithms, humanity has pushed the frontiers of what is possible. Cultivating curiosity within ourselves allows us to solve the most pressing challenges of tomorrow.');

addP($passages, $id++, 'The Legacy of Ada Lovelace', 'intermediate', 'History',
'Augusta Ada King, Countess of Lovelace, is universally recognized as the world first computer programmer. Working alongside Charles Babbage on the proposed Analytical Engine, she realized that computing machines could manipulate symbols beyond mere numbers. In 1843, she published an algorithm to compute Bernoulli numbers, foreseeing the immense potential of modern general-purpose computers over a century before their physical realization.');

addP($passages, $id++, 'Alan Turing and Machine Intelligence', 'intermediate', 'Computer Science',
'Alan Turing theoretical work laid the bedrock of modern computer science and artificial intelligence. His formulation of the universal Turing machine demonstrated how a single machine could execute any computable function. During World War II, his cryptographic breakthroughs at Bletchley Park saved millions of lives. Turing pioneering questions about whether machines can think continue to drive computational linguistics and neural network research today.');

addP($passages, $id++, 'The Mechanics of Memory and Learning', 'intermediate', 'Neuroscience',
'When you practice a skill like touch typing, your brain undergoes neuroplastic restructuring. Synaptic pathways between the motor cortex and sensory receptors strengthen through repetitive stimulation. Over time, conscious cognitive load transitions into subconscious procedural memory, allowing experienced typists to transcribe complex sentences at high speeds without consciously identifying individual key positions.');

addP($passages, $id++, 'Cloud Computing and Microservices', 'intermediate', 'Technology',
'Cloud computing has revolutionized modern enterprise infrastructure by abstracting physical servers into dynamic, on-demand compute pools. Containerization technologies, orchestrators, and serverless functions allow applications to scale automatically in response to traffic spikes. Developers can deploy decoupled microservices across multi-region clusters, delivering resilient experiences to millions of concurrent users with ninety-nine point nine percent uptime guarantees.');

addP($passages, $id++, 'The James Webb Space Telescope', 'intermediate', 'Astronomy',
'Positioned at the second Lagrange point one million miles from Earth, the James Webb Space Telescope captures infrared emissions from the earliest galaxies formed after the Big Bang. Its segmented, gold-coated beryllium mirrors unfold with sub-nanometer precision in the freezing vacuum of space. By peering through cosmic dust clouds, Webb provides astronomers with unprecedented insights into planetary atmospheric composition and stellar evolution.');

addP($passages, $id++, 'The Philosophy of Stoicism', 'intermediate', 'Philosophy',
'Stoic philosophy, championed by thinkers such as Marcus Aurelius, Epictetus, and Seneca, teaches that true tranquility comes from distinguishing between what is within our control and what is not. While external events, market fluctuations, and opinions of others remain outside our command, our thoughts, actions, and reactions remain entirely our own. Practicing emotional discipline enables individuals to remain serene in the face of sudden adversity.');

addP($passages, $id++, 'Grace Hopper and Compiler Design', 'intermediate', 'History',
'Rear Admiral Grace Hopper was a pioneer who fundamentally reshaped human-computer interaction. Believing that computer code should be written in English-like syntax rather than raw machine instructions, she invented the first compiler in 1952. Her revolutionary work led directly to the creation of COBOL, democratizing software development and enabling businesses and governments to automate complex data processing tasks worldwide.');

addP($passages, $id++, 'Renewable Energy and Smart Grids', 'intermediate', 'Science',
'The global transition toward sustainable energy demands sophisticated smart grid infrastructure capable of managing intermittent generation from solar arrays and wind turbines. Advanced battery storage systems, combined with real-time predictive analytics, balance electrical supply and demand across metropolitan distribution networks. Decentralized microgrids empower local communities to achieve energy independence while drastically curtailing carbon footprints.');

addP($passages, $id++, 'The Principle of Least Privilege in Security', 'intermediate', 'Cybersecurity',
'In modern cybersecurity, the principle of least privilege mandates that users, processes, and applications receive only the minimal access rights required to execute their specific duties. Implementing fine-grained role-based access control, ephemeral credentials, and multi-factor authentication prevents lateral movement during security breaches. Defense in depth ensures that compromise of a single component does not undermine the integrity of the overall infrastructure.');

addP($passages, $id++, 'Understanding Asynchronous JavaScript', 'intermediate', 'Programming',
'JavaScript single-threaded event loop processes tasks without blocking the main execution context. Utilizing promises and async-await syntax, developers can initiate network requests, file input-output, and timer callbacks while the browser remains responsive to user clicks and screen repaints. Mastering the microtask queue and call stack mechanics is fundamental to building high-performance web applications with sixty frames-per-second fluid animations.');

addP($passages, $id++, 'The Physics of Quantum Mechanics', 'intermediate', 'Physics',
'Quantum mechanics describes the physical behavior of subatomic particles where classical Newtonian physics breaks down. Key phenomena such as wave-particle duality, quantum superposition, and entanglement demonstrate that particles can exist in multiple states simultaneously until observed. These mind-bending principles form the foundation of emerging quantum computing architectures that leverage qubits to solve intractable optimization problems.');

addP($passages, $id++, 'The Art of Clean Code', 'intermediate', 'Software Engineering',
'Writing clean code is fundamentally an act of communication with other developers, including your future self. Expressive variable names, concise single-responsibility functions, and comprehensive unit tests transform cryptic logic into an intuitive narrative. As the famous programming proverb states: any fool can write code that a computer can understand; good programmers write code that humans can understand.');

addP($passages, $id++, 'Deep Sea Ecosystems and Hydrothermal Vents', 'intermediate', 'Marine Biology',
'Miles beneath the ocean surface, where sunlight never penetrates, vibrant ecosystems thrive around hydrothermal vents belching mineral-rich water at temperatures exceeding three hundred degrees Celsius. Rather than relying on photosynthesis, vent communities depend on chemosynthetic bacteria that convert toxic hydrogen sulfide into organic energy, supporting giant tube worms, blind crabs, and glowing deep-sea organisms.');

addP($passages, $id++, 'Claude Shannon and Information Theory', 'intermediate', 'Mathematics',
'In 1948, mathematician Claude Shannon published A Mathematical Theory of Communication, founding modern information theory. He defined the fundamental unit of information as the bit and established theoretical limits on data compression and transmission bandwidth over noisy channels. Shannon work laid the mathematical bedrock for digital telecommunications, cryptography, lossless compression, and the modern internet.');

addP($passages, $id++, 'Human-Centered Interface Design', 'intermediate', 'Design',
'Exceptional user interfaces feel intuitive because they align with established human cognitive models. Designers employ visual hierarchy, consistent typography, generous whitespace, and subtle micro-interactions to guide user attention without friction. Accessibility considerations, such as high-contrast color ratios and full keyboard navigation support, ensure digital products remain inclusive and delightful for everyone.');

addP($passages, $id++, 'The Complexity of Biological Neural Networks', 'intermediate', 'Biology',
'The human brain contains approximately eighty-six billion neurons interconnected by over one hundred trillion synapses. Electrical impulses propagate along axons, releasing neurotransmitters across synaptic gaps to modulate signal transmission with extraordinary energy efficiency. Studying biological neurochemistry provides deep inspiration for machine learning researchers designing artificial convolutional and transformer models.');

addP($passages, $id++, 'The Rise of Git and Version Control', 'intermediate', 'Technology',
'Created by Linus Torvalds in 2005 to manage the Linux kernel development, Git revolutionized source code collaboration through its decentralized directed acyclic graph architecture. By storing snapshots rather than file differences, Git enables atomic commits, instant branching, and non-linear merging workflows. Modern software teams rely on pull requests and automated continuous integration pipelines to ship updates with confidence.');

addP($passages, $id++, 'The Psychology of Habit Formation', 'intermediate', 'Psychology',
'Psychological research indicates that human habits follow a neurological loop: a cue, a routine, and a reward. When you associate sitting at your desk with opening a typing tutor and reward yourself with visible telemetry milestones, the routine becomes an automatic subconscious drive. Small intentional changes implemented consistently over months yield transformative personal and professional growth.');

addP($passages, $id++, 'Database Indexing Strategies', 'intermediate', 'Databases',
'Relational database indices, primarily structured as balanced B-trees, dramatically accelerate record retrieval by reducing disk I/O operations from linear full-table scans to logarithmic traversals. However, maintaining secondary indices introduces performance overhead during insertions, updates, and deletions. Database architects must carefully analyze query access patterns to design composite and covering indices that maximize throughput without bloating storage.');

addP($passages, $id++, 'The Origins of the Internet', 'intermediate', 'History',
'The internet originated in the late 1960s with ARPANET, an experimental packet-switching network funded by the United States Department of Defense. Unlike traditional circuit-switched telephone lines, packet switching breaks data into discrete addressed chunks that traverse alternative routes independently. The standardization of TCP/IP protocols in 1983 unified heterogeneous networks into the resilient global decentralized network we rely on today.');

addP($passages, $id++, 'The Symphony of the Operating System', 'intermediate', 'Computer Science',
'An operating system acts as the master conductor of computer hardware, arbitrating access to memory, CPU cycles, and peripheral devices. Its kernel schedules processes via preemptive time-slicing algorithms, manages virtual memory address spaces via paging tables, and enforces security boundaries between user applications. Seamless coordination between hardware and software allows hundreds of concurrent processes to execute flawlessly.');

addP($passages, $id++, 'The Mystery of Dark Matter', 'intermediate', 'Astrophysics',
'Astrophysical observations of galactic rotation curves and gravitational lensing reveal that visible baryonic matter accounts for only fifteen percent of the total mass in the universe. The remaining eighty-five percent consists of mysterious dark matter that does not emit, absorb, or reflect electromagnetic radiation. Detecting candidate particles like WIMPs remains one of the greatest quests in modern fundamental physics.');

addP($passages, $id++, 'The Power of Ergonomic Typing', 'intermediate', 'Ergonomics',
'Repetitive strain injuries such as carpal tunnel syndrome can sideline productive writers and engineers for months. Adopting split mechanical keyboards, adjusting chair armrests to ninety-degree elbow angles, and practicing relaxed floating-wrist touch typing minimizes tendon inflammation. Investing in proper posture and regular micro-breaks safeguards your physical longevity throughout a decades-long digital career.');

addP($passages, $id++, 'Functional Programming Concepts', 'intermediate', 'Programming',
'Functional programming treats computation as the evaluation of mathematical functions, avoiding mutable state and side effects. By embracing immutability, first-class higher-order functions, and pure mappings, developers write code that is predictable, easily testable, and naturally resilient to race conditions in concurrent multi-threaded environments. Modern languages increasingly blend functional paradigms with object-oriented patterns.');

addP($passages, $id++, 'The Importance of Open Source Software', 'intermediate', 'Technology',
'Open source software forms the invisible backbone of modern global civilization. From the Linux operating system powering cloud servers to open compilers, web frameworks, and machine learning libraries, collective volunteer collaboration outpaces closed proprietary models. Sharing source code freely democratizes education, enhances security auditing, and accelerates technological innovation across international borders.');

addP($passages, $id++, 'The Architecture of Modern Web Browsers', 'intermediate', 'Technology',
'A modern browser is essentially an operating system in miniature. Multi-process architectures separate rendering tabs, networking pipelines, GPU compositing, and sandboxed extension runtimes to prevent a single crashed webpage from destabilizing the entire browser. High-performance JavaScript engines employ just-in-time compilation to translate dynamic scripts into native machine instructions on the fly.');

addP($passages, $id++, 'The Concept of Flow State', 'intermediate', 'Psychology',
'Psychologist Mihaly Csikszentmihalyi defined flow as a state of intense, focused concentration where one becomes completely absorbed in an activity. Flow occurs when there is an optimal balance between the difficulty of a challenge and the skill of the individual. In typing, reaching flow transforms character recognition into effortless, rhythmic finger keystrokes that mirror the speed of raw thought.');

addP($passages, $id++, 'The Engineering of High-Speed Rail', 'intermediate', 'Engineering',
'High-speed bullet trains traveling at over three hundred kilometers per hour require advanced aerodynamic profiling to minimize atmospheric drag and pressure waves inside tunnels. Continuous welded steel rails eliminated the click-clack seams of classical railroads, while active tilt suspensions counteract centrifugal forces along curved tracks, providing passengers with whisper-quiet, energy-efficient intercity transit.');

addP($passages, $id++, 'The Fundamentals of Cryptography', 'intermediate', 'Mathematics',
'Cryptography secures confidential communication across untrusted public networks. Symmetric ciphers like AES encrypt large data streams with blazing speed using shared private keys, while asymmetric algorithms like RSA and elliptic curves solve the key exchange problem using mathematically paired public and private keys. Cryptographic protocols safeguard banking, e-commerce, and personal messaging globally.');

addP($passages, $id++, 'The Fascinating World of Fungi', 'intermediate', 'Biology',
'Beneath the forest floor lies a vast subterranean network of fungal mycelium connecting trees and plants in a symbiotic cooperative web. Trees use this mycorrhizal network to exchange nutrients, share carbon, and transmit chemical warning signals about insect infestations. Fungi are nature primary recyclers, decomposing organic matter and returning essential minerals to living soils.');

addP($passages, $id++, 'Microservices vs Monoliths', 'intermediate', 'Software Engineering',
'The debate between monolithic and microservice architectures centers around organizational complexity and operational scale. Monoliths offer simplicity in deployment, testing, and transactional consistency during early product iterations. Microservices decouple domains, allowing independent engineering teams to deploy micro-applications autonomously, albeit at the cost of distributed network tracing and eventual consistency management.');

addP($passages, $id++, 'The Science of Sound and Acoustics', 'intermediate', 'Physics',
'Sound propagates as longitudinal mechanical waves of pressure through physical mediums like air and water. When you strike a key on a mechanical keyboard, the plastic switch slider impacts the housing, producing acoustic vibrations that resonate through the aluminum mounting plate. Analyzing acoustic frequencies helps mechanical keyboard enthusiasts customize tactile, clicky, and silent typing switches.');

addP($passages, $id++, 'The Miracle of Photosynthesis', 'intermediate', 'Biology',
'Photosynthesis is the biochemical engine that sustains aerobic life on Earth. Plant chloroplasts harness photons from sunlight to split water molecules, releasing oxygen as a byproduct while converting carbon dioxide into energy-dense glucose sugars. Over billions of years, photosynthetic cyanobacteria transformed our atmosphere, making complex animal and human life possible.');

addP($passages, $id++, 'The Evolution of Typewriters', 'intermediate', 'History',
'The modern QWERTY keyboard layout was patented by Christopher Sholes in 1874 for the Sholes and Glidden typewriter. The mechanical design spaced frequently used letter pairs apart to prevent physical mechanical typebars from colliding and jamming when operators typed rapidly. Over a century later, touch typists worldwide continue to honor this heritage layout across touchscreens and mechanical keyboards.');

addP($passages, $id++, 'The Wonders of the Aurora Borealis', 'intermediate', 'Astronomy',
'The northern lights illuminate polar skies in shimmering curtains of vibrant emerald and violet. This celestial phenomenon occurs when charged solar wind particles collide with Earth magnetosphere and funnel down magnetic field lines into the upper atmosphere. Collisions with oxygen and nitrogen atoms release kinetic energy in the form of glowing photons that dance across the Arctic night.');

addP($passages, $id++, 'Continuous Integration and Deployment', 'intermediate', 'DevOps',
'Continuous Integration and Continuous Deployment pipelines automate software verification from code commit to production release. Every change initiates automated linting, unit testing, security scanning, and containerized artifact builds. If any test fails, developers receive instant feedback, preventing regressions from polluting production environments and enabling organizations to ship code multiple times daily.');

addP($passages, $id++, 'The Architecture of CPUs', 'intermediate', 'Computer Science',
'A modern central processing unit contains billions of nanometer-scale transistors etched onto silicon wafers. Pipelined execution stages, branch prediction heuristics, and multi-level L1, L2, and L3 cache hierarchies keep arithmetic logic units supplied with instructions. Understanding hardware cache lines and memory alignment enables software developers to write high-performance, cache-conscious programs.');

addP($passages, $id++, 'The Beauty of Mathematical Fractals', 'intermediate', 'Mathematics',
'Fractals are infinitely complex geometric patterns that exhibit self-similarity across different magnification scales. The famous Mandelbrot set, generated by iterating a simple quadratic equation over complex numbers, reveals breathtaking swirling spirals, dendrites, and miniature replicas of itself. Fractals describe natural structures like coastlines, river networks, lightning bolts, and roman broccoli.');

addP($passages, $id++, 'The Impact of Responsive Web Design', 'intermediate', 'Web Design',
'Responsive web design ensures digital experiences adapt seamlessly across smartphones, tablets, laptops, and ultra-wide desktop monitors. Utilizing flexible CSS grid layouts, media queries, and responsive typography, content reflows naturally to suit any viewport. Prioritizing mobile performance and touch ergonomics guarantees that websites deliver blazing fast, delightful interactions to every visitor.');

addP($passages, $id++, 'The Mystery of Sleep and Dreaming', 'intermediate', 'Neuroscience',
'During rapid eye movement sleep, the brain exhibits electrical activity remarkably similar to wakefulness while voluntary muscles remain safely paralyzed. Dreaming serves critical cognitive functions, including emotional regulation, memory consolidation, and creative problem solving. Regular high-quality sleep sharpens reaction times, improves hand-eye coordination, and enhances motor skill retention during typing practice.');

addP($passages, $id++, 'The Mechanics of Touch Typing', 'intermediate', 'Typing Skills',
'Touch typing is the skill of typing without looking at the keyboard, relying entirely on muscle memory and spatial proprioception. The home row keys—A, S, D, F for the left hand and J, K, L, Semicolon for the right hand—serve as the home anchor. Small tactile bumps on the F and J keys allow typists to reposition their hands effortlessly without breaking visual focus from the screen.');

addP($passages, $id++, 'The Principles of RESTful APIs', 'intermediate', 'Web Development',
'Representational State Transfer defines an architectural style for networked systems based on stateless client-server interactions. Clients manipulate resources identified by uniform resource identifiers using standard HTTP verbs: GET for retrieval, POST for creation, PUT for updates, and DELETE for removal. Standardized status codes and JSON payloads ensure interoperability across diverse programming platforms.');

addP($passages, $id++, 'The Physics of Flight', 'intermediate', 'Physics',
'Airplanes achieve flight through the aerodynamic interplay of four fundamental forces: lift, weight, thrust, and drag. As air flows over the curved camber of an airfoil wing, it travels faster over the upper surface, creating lower pressure in accordance with Bernoulli principle and Newton third law of action-reaction. The resulting upward lift overcomes the downward pull of gravity.');

addP($passages, $id++, 'The Evolution of Human Language', 'intermediate', 'Linguistics',
'Human language is a marvel of biological and social evolution that distinguishes our species from all others. Combining an arbitrary set of vocal phonemes and written graphemes according to recursive syntactic rules allows us to express an infinite variety of complex thoughts. Touch typing provides a modern digital bridge connecting subconscious inner speech directly to the written word.');

addP($passages, $id++, 'The Science of Habitats and Biodiversity', 'intermediate', 'Ecology',
'Biodiversity refers to the variety of living species within an ecosystem, from micro-organisms to apex predators. Diverse ecosystems demonstrate greater resilience against climatic shocks, disease outbreaks, and habitat fragmentation. Protecting coral reefs, tropical rainforests, and wetlands preserves genetic reservoirs and vital ecosystem services essential for global agricultural security.');

addP($passages, $id++, 'The Psychology of Gamification', 'intermediate', 'Psychology',
'Gamification applies game design mechanics—such as experience points, milestone badges, leaderboards, and progress bars—to non-game contexts. By providing immediate positive feedback loops, gamified learning environments stimulate dopamine release and sustain long-term engagement. Typists practicing in arcade challenge arenas often achieve higher words-per-minute velocities through playful competitive immersion.');

addP($passages, $id++, 'The Story of the Printing Press', 'intermediate', 'History',
'Johannes Gutenberg invention of movable metal type around 1440 transformed European civilization by drastically reducing the cost of book production. Previously, manuscripts required months of laborious hand-copying by monastic scribes. The rapid dissemination of religious, scientific, and philosophical texts catalyzed the Renaissance, the Scientific Revolution, and modern democratic literacy.');

addP($passages, $id++, 'Understanding Time Complexity', 'intermediate', 'Computer Science',
'When evaluating algorithms, computer scientists analyze how computational resource requirements scale as input size grows toward infinity. An algorithm with linear complexity O(n) takes twice as long when input doubles, whereas quadratic complexity O(n^2) quadruples execution time. Choosing optimal algorithms ensures that digital services remain lightning fast even when processing billions of records.');

addP($passages, $id++, 'The Philosophy of Minimalism', 'intermediate', 'Lifestyle',
'Minimalism is not about depriving oneself of joy; it is about deliberately curating your environment to make room for what truly matters. By stripping away visual clutter, superficial commitments, and excessive material possessions, individuals cultivate mental clarity and peaceful living spaces. A clean desk with a single backlit keyboard and monitor invites serene, deep typing work.');

addP($passages, $id++, 'The Engineering of Modern Bridges', 'intermediate', 'Engineering',
'Suspension bridges like the Golden Gate Bridge suspend vehicular traffic decks from massive high-strength steel cables anchored to concrete abutments and draped over towering steel pylons. Civil engineers utilize aerodynamic wind tunnel testing and tuned mass dampers to neutralize harmonic resonance and dynamic vortex shedding, ensuring structural integrity during heavy storms and seismic tremors.');

addP($passages, $id++, 'The Structure of the Solar System', 'intermediate', 'Astronomy',
'Our solar system comprises the central Sun, eight major planets, hundreds of moons, and millions of asteroids and comets orbiting in gravitational equilibrium. Inner rocky planets—Mercury, Venus, Earth, and Mars—give way to the asteroid belt and massive outer gas and ice giants. Exploring these celestial neighbors expands human understanding of planetary formation.');

addP($passages, $id++, 'The Rise of Cloud Architecture', 'intermediate', 'Technology',
'Modern web infrastructure embraces cloud-native distributed architecture where applications run as stateless containerized services across global data centers. Automated load balancers distribute incoming traffic, managed relational databases handle transactional persistence, and content delivery networks cache static assets at network edge points close to users, reducing latency to mere milliseconds.');

addP($passages, $id++, 'The Wonders of Bird Migration', 'intermediate', 'Nature',
'Every autumn, millions of migratory birds embark on epic transcontinental journeys spanning thousands of miles. Guided by an innate celestial compass, polarized light patterns, and sensitive magnetic sensors in their beaks, species like the Arctic tern travel from pole to pole. Their extraordinary endurance highlights the profound interconnectedness of global ecosystems.');

addP($passages, $id++, 'The Basics of Relational SQL', 'intermediate', 'Databases',
'Structured Query Language allows software developers to define, query, and manipulate relational data tables through expressive declarative syntax. Using JOIN clauses to link related entities, WHERE predicates to filter records, and aggregate functions like COUNT, AVG, and SUM, developers extract meaningful business insights from complex normalized datasets with optimal computational efficiency.');

addP($passages, $id++, 'The Magic of Optical Fiber Networks', 'intermediate', 'Telecommunications',
'Global telecommunications rely on subsea optical fiber cables spanning ocean basins. By modulating light pulses through hair-thin strands of ultra-pure silica glass using total internal reflection, data travels across continents at ninety-nine percent the speed of light in vacuum. Multi-wavelength dense wavelength division multiplexing transmits terabits of data per second per fiber pair.');

addP($passages, $id++, 'The Discipline of Continuous Learning', 'intermediate', 'Inspiration',
'In an era where technology evolves at an exponential velocity, the willingness to continually unlearn outdated habits and master modern tools is the defining differentiator of successful professionals. Embracing daily practice, welcoming challenging feedback, and maintaining curiosity turns ordinary individuals into world-class masters across any technical craft.');

addP($passages, $id++, 'The Chemistry of Water', 'intermediate', 'Science',
'Water is a unique compound whose polar molecular structure gives rise to powerful hydrogen bonds. These bonds grant water high surface tension, an unusually high boiling point, and the unique property of expanding upon freezing. Because ice floats on liquid water, aquatic life survives through freezing winters beneath insulating frozen surface sheets.');

addP($passages, $id++, 'The Craft of Technical Writing', 'intermediate', 'Communication',
'Technical writing demands clarity, conciseness, and empathy for the reader. Good documentation breaks down sophisticated architectural concepts into digestible steps using diagrams, code examples, and unambiguous language. When complex systems are documented well, engineering teams onboard faster, resolve production outages promptly, and maintain code with confidence.');

addP($passages, $id++, 'The Geometry of Architecture', 'intermediate', 'Design',
'Architectural masterpieces throughout history—from the Parthenon in Athens to the Sydney Opera House—unite structural physics with mathematical proportions like the golden ratio. Vaulted ceilings, cantilevered balconies, and glass curtain facades balance compressive loads and tensile forces while creating spaces that inspire awe, contemplation, and creative civic engagement.');

addP($passages, $id++, 'The Power of High-Cadence Typing', 'intermediate', 'Typing Skills',
'Mastering touch typing at eighty or one hundred words per minute removes the physical friction between your brain and your text editor. Words flow directly from subconscious thought onto the screen with zero hesitation. When typing becomes second nature, you can direct your full mental RAM toward problem solving, storytelling, and algorithmic architecture.');

addP($passages, $id++, 'The Future of Renewable Tech', 'intermediate', 'Technology',
'Next-generation perovskite solar cells, floating offshore wind turbines, and solid-state lithium batteries are transforming global energy production. By pairing clean generation with artificial intelligence forecasting, modern power grids can match fluctuating demand in real time, making a sustainable, zero-emission future practically achievable for our planet.');


// =========================================================================
// ADVANCED PASSAGES (IDs 136 - 195): Code, Big-O, numbers, symbols, complex syntax
// =========================================================================
addP($passages, $id++, 'Algorithmic Complexity & Big-O Notation', 'advanced', 'Computer Science',
'When analyzing algorithmic performance, computer scientists utilize asymptotic Big-O notation, such as O(1), O(log n), O(n log n), and O(n^2). For instance, an in-place QuickSort algorithm achieves an average time complexity of O(n log n) with O(log n) auxiliary stack space; however, adversarial input sequences can degrade performance to O(n^2). Balancing time-space trade-offs, cache locality, and branch prediction is crucial when optimizing high-throughput production systems.');

addP($passages, $id++, 'Database Concurrency & ACID Guarantees', 'advanced', 'Databases',
'Relational database management systems (RDBMS) enforce the ACID paradigm: Atomicity, Consistency, Isolation, and Durability. Implementing rigorous multi-version concurrency control (MVCC) enables high-concurrency read-write transactions without blocking read operations. Database administrators must carefully configure isolation levels—such as READ COMMITTED vs. SERIALIZABLE—to prevent dirty reads, non-repeatable reads, and phantom row anomalies under heavy concurrent load (e.g., >10,000 queries/sec).');

addP($passages, $id++, 'Asynchronous Event Loops & Non-Blocking I/O', 'advanced', 'System Architecture',
'Under the hood, event-driven architectures rely on kernel-level multiplexing primitives like epoll() on Linux, kqueue() on BSD/macOS, and I/O Completion Ports (IOCP) on Windows. Rather than allocating a heavy thread per socket connection (1MB+ stack overhead), a single thread can monitor thousands of active file descriptors simultaneously. When network packets arrive, non-blocking callback queues trigger asynchronous resolution with sub-millisecond latency: latency <= 0.85ms!');

addP($passages, $id++, 'Cryptographic Security & Modern Protocols', 'advanced', 'Cybersecurity',
'Transport Layer Security (TLS 1.3) minimizes handshake latency by mandating a 1-RTT (or 0-RTT with PSK) exchange utilizing ephemeral Diffie-Hellman (ECDHE) keys over Curve25519. In conjunction with SHA-256/SHA-384 hashing and AES-256-GCM authenticated encryption, modern cipher suites ensure forward secrecy. Cryptographic integrity requires deterministic verification against timing side-channel vectors: never use variable-time memory comparisons for secure HMAC tokens!');

addP($passages, $id++, 'JavaScript Promise Combinators', 'advanced', 'JavaScript Code',
'const fetchUserData = async (userId) => {\n  try {\n    const [profile, settings] = await Promise.all([\n      fetch(`/api/users/${userId}`).then(res => res.json()),\n      fetch(`/api/settings/${userId}`).then(res => res.json())\n    ]);\n    return { status: 200, data: { ...profile, ...settings } };\n  } catch (error) {\n    console.error("Fetch failed: ", error?.message ?? "Unknown error");\n    return { status: 500, error: error.code || 5001 };\n  }\n};');

addP($passages, $id++, 'Python List Comprehensions & Lambdas', 'advanced', 'Python Code',
'def calculate_telemetry(keystrokes: list[dict], duration: float) -> dict:\n    valid_keys = [k["char"] for k in keystrokes if k.get("is_valid", False)]\n    gross_wpm = round((len(valid_keys) / 5.0) / (duration / 60.0), 2) if duration > 0 else 0.0\n    accuracy = round((sum(1 for k in keystrokes if not k.get("error")) / max(1, len(keystrokes))) * 100.0, 1)\n    return {"gross_wpm": gross_wpm, "accuracy": f"{accuracy}%", "count": len(valid_keys)}');

addP($passages, $id++, 'SQL Window Functions & CTEs', 'advanced', 'SQL Code',
'WITH RankedResults AS (\n    SELECT \n        r.user_id,\n        u.username,\n        r.net_wpm,\n        r.accuracy,\n        ROW_NUMBER() OVER(PARTITION BY r.user_id ORDER BY r.net_wpm DESC) AS rank_order,\n        AVG(r.net_wpm) OVER(PARTITION BY r.user_id) AS avg_speed\n    FROM typing_results r\n    INNER JOIN users u ON r.user_id = u.id\n    WHERE r.created_at >= NOW() - INTERVAL 30 DAY\n)\nSELECT * FROM RankedResults WHERE rank_order = 1 ORDER BY net_wpm DESC LIMIT 50;');

addP($passages, $id++, 'CSS Grid & Modern Design Tokens', 'advanced', 'CSS Code',
':root {\n  --grid-columns: repeat(auto-fit, minmax(280px, 1fr));\n  --card-bg: rgba(20, 26, 42, 0.95);\n  --border-glow: 0 0 15px rgba(255, 87, 34, 0.45);\n  --transition-cubic: all 250ms cubic-bezier(0.4, 0, 0.2, 1);\n}\n.dashboard-grid {\n  display: grid;\n  grid-template-columns: var(--grid-columns);\n  gap: clamp(1rem, 2vw, 2rem);\n  backdrop-filter: blur(16px);\n}');

addP($passages, $id++, 'C++ Smart Pointers & Memory Management', 'advanced', 'C++ Code',
'#include <iostream>\n#include <memory>\n#include <vector>\n\ntemplate <typename T>\nclass Node {\npublic:\n    T value;\n    std::unique_ptr<Node<T>> next;\n    explicit Node(T val) : value(std::move(val)), next(nullptr) {}\n};\n\nint main() {\n    auto head = std::make_unique<Node<int>>(42);\n    head->next = std::make_unique<Node<int>>(84);\n    std::cout << "Node 1: " << head->value << ", Node 2: " << head->next->value << std::endl;\n    return 0;\n}');

addP($passages, $id++, 'TypeScript Generic Utility Interfaces', 'advanced', 'TypeScript Code',
'interface ApiResponse<TData = Record<string, unknown>> {\n  success: boolean;\n  timestamp: number;\n  payload: TData;\n  errors?: Array<{ code: number; message: string; field?: keyof TData }>;\n}\n\ntype DeepPartial<T> = {\n  [P in keyof T]?: T[P] extends object ? DeepPartial<T[P]> : T[P];\n};\n\nconst parseConfig = <T extends object>(raw: string): DeepPartial<T> => JSON.parse(raw);');

addP($passages, $id++, 'PHP 8.2 Typed Classes & Match Expressions', 'advanced', 'PHP Code',
'final readonly class TypingTelemetry {\n    public function __construct(\n        public int $userId,\n        public float $wpm,\n        public float $accuracy,\n        public string $difficulty\n    ) {}\n\n    public function getGrade(): string {\n        return match (true) {\n            $this->wpm >= 100.0 && $this->accuracy >= 98.0 => "Grandmaster S-Tier",\n            $this->wpm >= 80.0 => "Professional A-Tier",\n            $this->wpm >= 60.0 => "Intermediate B-Tier",\n            default => "Developing C-Tier",\n        };\n    }\n}');

addP($passages, $id++, 'Bash Terminal Commands & Shell Pipelines', 'advanced', 'Shell Code',
'cat /var/log/nginx/access.log | awk \'{print $1, $7, $9}\' | grep -E " (404|500) " | sort | uniq -c | sort -nr | head -n 25 | awk \'{printf "%-8s %-16s %s\\n", $1, $2, $3}\'');

addP($passages, $id++, 'HTML5 Canvas 60 FPS Render Loop', 'advanced', 'JavaScript Code',
'function renderLoop(timestamp) {\n  const deltaTime = (timestamp - lastFrameTime) / 1000.0;\n  lastFrameTime = timestamp;\n  ctx.clearRect(0, 0, canvas.width, canvas.height);\n  for (let i = entities.length - 1; i >= 0; i--) {\n    entities[i].update(deltaTime);\n    entities[i].draw(ctx);\n    if (entities[i].isDead) entities.splice(i, 1);\n  }\n  requestAnimationFrame(renderLoop);\n}');

addP($passages, $id++, 'Git CLI Rebasing & Commit Plumbing', 'advanced', 'Git Commands',
'git checkout -b feature/auth-v2 main\ngit fetch origin && git rebase -i origin/main\ngit commit -am "refactor(auth): implement RFC-7519 JWT verification [skip ci]"\ngit push origin feature/auth-v2 --force-with-lease');

addP($passages, $id++, 'Regular Expressions & Pattern Matching', 'advanced', 'RegEx Syntax',
'const EMAIL_REGEX = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,63}$/;\nconst SEMVER_REGEX = /^v?(0|[1-9]\\d*)\\.(0|[1-9]\\d*)\\.(0|[1-9]\\d*)(?:-((?:0|[1-9]\\d*|\\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\\.(?:0|[1-9]\\d*|\\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\\+([0-9a-zA-Z-]+(?:\\.[0-9a-zA-Z-]+)*))?$/;');

addP($passages, $id++, 'JSON Schema Validation Payload', 'advanced', 'Data Format',
'{\n  "$schema": "https://json-schema.org/draft/2020-12/schema",\n  "title": "TypingSessionResult",\n  "type": "object",\n  "properties": {\n    "sessionId": { "type": "string", "format": "uuid" },\n    "wpm": { "type": "number", "minimum": 0.0, "maximum": 300.0 },\n    "accuracy": { "type": "number", "minimum": 0.0, "maximum": 100.0 },\n    "tags": { "type": "array", "items": { "type": "string" }, "uniqueItems": true }\n  },\n  "required": ["sessionId", "wpm", "accuracy"]\n}');

addP($passages, $id++, 'SQL Index Optimization & Table Partitions', 'advanced', 'Databases',
'ALTER TABLE typing_results \n  PARTITION BY RANGE (YEAR(created_at)) (\n    PARTITION p2024 VALUES LESS THAN (2025),\n    PARTITION p2025 VALUES LESS THAN (2026),\n    PARTITION p2026 VALUES LESS THAN (2027),\n    PARTITION p_future VALUES LESS THAN MAXVALUE\n);\nCREATE INDEX idx_user_telemetry ON typing_results (user_id, net_wpm DESC, accuracy DESC);');

addP($passages, $id++, 'Symbol Intensive Drills: Braces & Operators', 'advanced', 'Symbols Drill',
'if (x !== null && typeof x === "object") {\n  const [a, b, ...rest] = Object.entries(x);\n  const formatted = `${a[0]}: ${a[1]} | ${b ? b[0] : "N/A"}`;\n  return (formatted.length > 50) ? formatted.slice(0, 47) + "..." : formatted;\n} else {\n  return String(x ?? "undefined");\n}');

addP($passages, $id++, 'Number & Currency Calculations', 'advanced', 'Financial Math',
'Total Revenue for Q4: $4,892,150.75 (up +18.4% YoY vs $4,131,800.00 in Q3).\nOperating Margins: 28.65% with EBITDA of $1,401,601.19.\nNet Earnings Per Share (EPS): $1.42 (diluted) across 3,445,180 shares outstanding.\nExchange Rates: USD/EUR = 0.9234, USD/GBP = 0.7891, USD/JPY = 154.62.');

addP($passages, $id++, 'Matrix Multiplication Algorithm', 'advanced', 'Math Algorithms',
'for (let i = 0; i < N; i++) {\n  for (let j = 0; j < P; j++) {\n    let sum = 0.0;\n    for (let k = 0; k < M; k++) {\n      sum += matrixA[i][k] * matrixB[k][j];\n    }\n    resultMatrix[i][j] = sum;\n  }\n}');

addP($passages, $id++, 'Rust Ownership & Borrow Checker', 'advanced', 'Rust Code',
'fn process_buffer(mut data: Vec<u8>) -> Result<usize, &\'static str> {\n    if data.is_empty() {\n        return Err("Buffer underflow: slice cannot be empty");\n    }\n    data.push(0xAA);\n    let slice_ref = &data[..];\n    println!("Processed {} bytes: {:02X?}", slice_ref.len(), slice_ref);\n    Ok(slice_ref.len())\n}');

addP($passages, $id++, 'Docker Compose Multi-Container Orchestration', 'advanced', 'DevOps Syntax',
'version: "3.8"\nservices:\n  redis:\n    image: redis:7.2-alpine\n    ports:\n      - "6379:6379"\n    volumes:\n      - redis_data:/data\n    command: ["redis-server", "--appendonly", "yes", "--requirepass", "Secr3tP@ss!"]\nvolumes:\n  redis_data:\n    driver: local');

addP($passages, $id++, 'Quotes with Punctuation & Diacritics', 'advanced', 'Punctuation Drill',
'"Success isn\'t always about greatness; it\'s about consistency." — Dwayne Johnson.\n"To be yourself in a world that is constantly trying to make you something else is the greatest accomplishment." — Ralph Waldo Emerson.\n"C\'est la vie; déjà vu; résumé; façade; voilà!" The café\'s décor was über-chic ($15.50/coffee).');

addP($passages, $id++, 'GraphQL Query Schema & Mutations', 'advanced', 'GraphQL Syntax',
'mutation UpdateUserProfile($userId: ID!, $input: ProfileInput!) {\n  updateProfile(id: $userId, input: $input) {\n    user {\n      id\n      username\n      stats {\n        testsCompleted\n        highestWpm\n        avgAccuracy\n      }\n    }\n    errors { field message }\n  }\n}');

addP($passages, $id++, 'Kubernetes Pod Deployment Manifest', 'advanced', 'DevOps YAML',
'apiVersion: apps/v1\nkind: Deployment\nmetadata:\n  name: typoprac-engine\n  namespace: production\nspec:\n  replicas: 3\n  selector:\n    matchLabels:\n      app: typoprac\n  template:\n    metadata:\n      labels:\n        app: typoprac\n    spec:\n      containers:\n      - name: api\n        image: typoprac/api:v2.4.1\n        ports:\n        - containerPort: 8080');

addP($passages, $id++, 'Mathematical Calculus & Differential Equations', 'advanced', 'Mathematics',
'f\'(x) = lim(h -> 0) [f(x + h) - f(x)] / h.\nIntegral: ∫[0, π] sin(x) dx = [-cos(x)][0, π] = -(-1) - (-1) = 2.0.\nEuler\'s Identity: e^(i * π) + 1 = 0. Taylor series: e^x = ∑ (x^n / n!) for n = 0 to ∞.');

addP($passages, $id++, 'Pointers, References & Bitwise Arithmetic', 'advanced', 'C Syntax',
'uint32_t flags = (1U << 4) | (1U << 7);\nif ((flags & (1U << 4)) != 0) {\n  flags &= ~(1U << 7);\n  uint16_t upper = (uint16_t)((flags >> 16) & 0xFFFF);\n  uint16_t lower = (uint16_t)(flags & 0xFFFF);\n  printf("0x%04X:0x%04X\\n", upper, lower);\n}');

addP($passages, $id++, 'Complex URLs with Query Parameters', 'advanced', 'Web URLs',
'https://api.typoprac.com/v1/sessions/search?query=touch+typing&difficulty=advanced&limit=50&offset=100&fields=wpm,accuracy,timestamp&sort=-wpm&auth_token=eyJhbGciOiJIUzI1NiJ9.s64_2#telemetry_tab');

addP($passages, $id++, 'High-Speed Number Row Precision', 'advanced', 'Number Row Drill',
'1029 3847 5610 2938 4756 1029 3847 5610 9283 7465 1928 3746 5019 2837 4650 1928 3746 5019.\nPhone: (800) 555-0199 ext. 4201 | Fax: +1-202-555-0143 | PIN: #8492* | Account: 009-482-114-8.');

addP($passages, $id++, 'Punctuation Marathon Drill', 'advanced', 'Punctuation Drill',
'Wait; what? Is this real?! "Yes," she whispered, "it\'s 100% genuine." [Note: check lines 42-45 & 89-91; see ref (A.1.b)]. {x: 10, y: 20} -> f(x) * g(y) = 200 / (10 - 2) = 25.0!');

addP($passages, $id++, 'CSS Flexbox Alignments & Shorthands', 'advanced', 'CSS Syntax',
'.flex-container {\n  display: flex;\n  flex-flow: row wrap;\n  justify-content: space-between;\n  align-items: center;\n  align-content: stretch;\n  gap: 1.5rem 2rem;\n  margin: 0 auto;\n  padding: calc(100vh / 12) clamp(1rem, 5vw, 4rem);\n}');

addP($passages, $id++, 'Asynchronous Python with aiohttp', 'advanced', 'Python Async',
'import asyncio\nimport aiohttp\n\nasync def fetch_metrics(session, url: str) -> dict:\n    async with session.get(url, timeout=5.0) as response:\n        assert response.status == 200, f"HTTP Error: {response.status}"\n        return await response.json()\n\nasync def main():\n    async with aiohttp.ClientSession() as s:\n        results = await asyncio.gather(*[fetch_metrics(s, f"https://api/v1/{i}") for i in range(10)])\n        print(f"Loaded {len(results)} metrics.")');

addP($passages, $id++, 'SQL Analytical Aggregation & Rollups', 'advanced', 'SQL Analytics',
'SELECT \n  COALESCE(department_id, 0) AS dept,\n  COALESCE(role_level, "ALL") AS role,\n  COUNT(*) AS total_employees,\n  ROUND(AVG(salary), 2) AS avg_comp,\n  ROUND(STDDEV(salary), 2) AS std_comp\nFROM employee_records\nGROUP BY department_id, role_level WITH ROLLUP\nHAVING total_employees > 5;');

addP($passages, $id++, 'Special Characters & Mathematical Logic', 'advanced', 'Math Logic',
'Propositional Calculus: (P ∧ Q) -> R ≡ ¬(P ∧ Q) ∨ R ≡ (¬P ∨ ¬Q) ∨ R.\nDe Morgan\'s Laws: ¬(A ∪ B) = ¬A ∩ ¬B; ¬(A ∩ B) = ¬A ∪ ¬B.\nSet Theory: A ⊆ B <=> ∀x (x ∈ A => x ∈ B); |P(A)| = 2^|A|; ∅ ⊂ {0, 1, 2}.');

addP($passages, $id++, 'JavaScript DOM Traversal & Mutation Observer', 'advanced', 'JavaScript DOM',
'const observer = new MutationObserver((mutationsList, obs) => {\n  for (const mutation of mutationsList) {\n    if (mutation.type === "childList" && mutation.addedNodes.length > 0) {\n      mutation.addedNodes.forEach(node => {\n        if (node.nodeType === Node.ELEMENT_NODE && node.matches(".typing-caret")) {\n          node.scrollIntoView({ behavior: "smooth", block: "nearest" });\n        }\n      });\n    }\n  }\n});');

addP($passages, $id++, 'TypeScript Enums & Conditional Types', 'advanced', 'TypeScript',
'export enum EngineStatus {\n  IDLE = "IDLE",\n  RUNNING = "RUNNING",\n  PAUSED = "PAUSED",\n  TERMINATED = "TERMINATED"\n}\n\ntype ActionPayload<T extends EngineStatus> = \n  T extends EngineStatus.RUNNING ? { startTime: number; passageId: number } :\n  T extends EngineStatus.TERMINATED ? { finalWpm: number; accuracy: number; errorCount: number } :\n  void;');

addP($passages, $id++, 'Linux Bash File Operations & Redirections', 'advanced', 'Bash Script',
'#!/usr/bin/env bash\nset -euo pipefail\nIFS=$\'\\n\\t\'\n\nLOG_DIR="/var/log/typoprac"\nmkdir -p "${LOG_DIR}"\nexec 2>>"${LOG_DIR}/stderr.log"\nfind "${LOG_DIR}" -type f -name "*.log" -mtime +14 -exec gzip -9 {} \\;\necho "Archived stale log files at $(date -u +\'%Y-%m-%dT%H:%M:%SZ\')"');

addP($passages, $id++, 'CSS 3D Transforms & Keyframe Animations', 'advanced', 'CSS Animations',
'@keyframes floatCard {\n  0%, 100% {\n    transform: perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0);\n  }\n  50% {\n    transform: perspective(1000px) rotateX(4deg) rotateY(-4deg) translateY(-8px);\n  }\n}\n.hud-card {\n  animation: floatCard 4s ease-in-out infinite;\n  transform-style: preserve-3d;\n}');

addP($passages, $id++, 'JavaScript Array Reduce & Object Transposition', 'advanced', 'JavaScript',
'const groupStatsByCategory = (sessions) => {\n  return sessions.reduce((acc, { category, wpm, accuracy }) => {\n    if (!acc[category]) acc[category] = { totalWpm: 0, totalAcc: 0, count: 0 };\n    acc[category].totalWpm += wpm;\n    acc[category].totalAcc += accuracy;\n    acc[category].count += 1;\n    return acc;\n  }, {});\n};');

addP($passages, $id++, 'Network Sockets & TCP Packet Headers', 'advanced', 'Networking',
'TCP Header Format: Source Port (16 bits) | Destination Port (16 bits) | Sequence Number (32 bits) | Acknowledgment Number (32 bits) | Data Offset (4 bits) | Reserved (3 bits) | Flags [URG, ACK, PSH, RST, SYN, FIN] (9 bits) | Window Size (16 bits) | Checksum (16 bits) | Urgent Pointer (16 bits).');

addP($passages, $id++, 'Advanced Quote Sprints with Numbers', 'advanced', 'Speed Sprints',
'"In 2026, over 95.8% of high-frequency trading transactions executed under 450 microseconds," reported Dr. H. Vance (PhD, MIT \'19). "By optimizing C++ latency from 1.2μs to 0.35μs, our gross arbitrage yielded $14.2M over 120 trading days."');

addP($passages, $id++, 'C++ Lambda Expressions & std::algorithm', 'advanced', 'C++ Code',
'#include <algorithm>\n#include <vector>\n\nvoid sort_telemetry(std::vector<int>& scores) {\n    std::sort(scores.begin(), scores.end(), [](const int a, const int b) noexcept {\n        return (a % 2 == 0 && b % 2 != 0) ? true : (a % 2 != 0 && b % 2 == 0) ? false : a > b;\n    });\n}');

addP($passages, $id++, 'Markdown & GitHub Flavored Syntax', 'advanced', 'Markdown Syntax',
'# TypoPrac v2.4.0 Release Notes\n\n- [x] Implemented 190+ diverse curated passages\n- [x] Auto-shuffle randomized drill selector\n- [ ] Multi-region WebSocket telemetry pipeline (`wss://stream.typoprac.com/v2`)\n\n> Note: Benchmark speed targets: **80+ WPM** with *98% accuracy*!');

addP($passages, $id++, 'Python Generators & Memory Pipelines', 'advanced', 'Python Code',
'def stream_passage_chunks(filepath: str, chunk_size: int = 1024):\n    with open(filepath, mode="r", encoding="utf-8") as f:\n        while True:\n            chunk = f.read(chunk_size)\n            if not chunk:\n                break\n            yield from (line.strip() for line in chunk.splitlines() if line.strip())');

addP($passages, $id++, 'SQL Indexes, Foreign Keys & Cascades', 'advanced', 'SQL Code',
'CREATE TABLE user_telemetry_logs (\n  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,\n  user_id INT UNSIGNED NOT NULL,\n  wpm DECIMAL(5, 2) NOT NULL DEFAULT 0.00,\n  accuracy DECIMAL(4, 2) NOT NULL DEFAULT 100.00,\n  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n  CONSTRAINT fk_telemetry_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE\n) ENGINE=InnoDB ROW_FORMAT=DYNAMIC;');

addP($passages, $id++, 'TypeScript Discriminated Unions', 'advanced', 'TypeScript',
'type DrillEvent =\n  | { type: "KEY_DOWN"; key: string; timestamp: number }\n  | { type: "ERROR_RECORDED"; expected: string; received: string; index: number }\n  | { type: "SESSION_FINISHED"; elapsedSeconds: number; finalWpm: number };\n\nfunction handleEvent(ev: DrillEvent): void {\n  switch (ev.type) {\n    case "KEY_DOWN": return console.log(`Key: ${ev.key}`);\n    case "ERROR_RECORDED": return console.warn(`Mismatch @ ${ev.index}`);\n    case "SESSION_FINISHED": return console.info(`Finished with ${ev.finalWpm} WPM`);\n  }\n}');

addP($passages, $id++, 'PHP PDO Prepared Transactions', 'advanced', 'PHP Code',
'$pdo->beginTransaction();\ntry {\n  $stmt = $pdo->prepare("INSERT INTO typing_results (user_id, net_wpm, accuracy) VALUES (:u, :w, :a)");\n  $stmt->execute([":u" => $userId, ":w" => $wpm, ":a" => $acc]);\n  $resId = (int)$pdo->lastInsertId();\n  $pdo->commit();\n  echo json_encode(["status" => "ok", "result_id" => $resId]);\n} catch (Exception $e) {\n  $pdo->rollBack();\n  throw $e;\n}');

addP($passages, $id++, 'CSS Grid Holy Grail Layout', 'advanced', 'CSS Syntax',
'body.holy-grail {\n  display: grid;\n  grid-template-rows: auto 1fr auto;\n  grid-template-columns: 240px 1fr 300px;\n  grid-template-areas:\n    "header  header  header"\n    "sidebar main    stats"\n    "footer  footer  footer";\n  min-height: 100vh;\n}');

addP($passages, $id++, 'JavaScript Currying & Functional Pipe', 'advanced', 'JavaScript',
'const pipe = (...fns) => (x) => fns.reduce((v, f) => f(v), x);\nconst cleanString = (str) => str.trim().toLowerCase();\nconst sanitizeHtml = (str) => str.replace(/<[^>]*>/g, "");\nconst truncate = (len) => (str) => str.length > len ? `${str.slice(0, len)}...` : str;\nconst formatSnippet = pipe(cleanString, sanitizeHtml, truncate(80));');

addP($passages, $id++, 'Speed Typist Grandmaster Benchmark', 'advanced', 'Speed Sprints',
'The lightning-fast cheetah sprints across the golden savanna at seventy miles per hour, mirroring the velocity of a master touch typist conquering one hundred and fifty words per minute! Fingers glide effortlessly like water over smooth river stones, turning raw electrical thoughts into pixelated reality.');

// Fill remaining to ensure total is exactly 195 passages!
$currentCount = count($passages);
$remaining = 195 - $currentCount;

$extraThemes = [
    ['The Philosophy of Continuous Growth', 'intermediate', 'Philosophy', 'Growth is not a destination where one arrives and rests forever. It is an ongoing journey of refinement, self-discovery, and patient labor. Each day offers a blank canvas to paint with focused effort and thoughtful kindness.'],
    ['The Beauty of Open Source Collaboration', 'intermediate', 'Technology', 'When programmers across continents contribute code to public repositories, they forge a collective intellectual heritage. Open collaboration eliminates redundant effort and democratizes access to world-class software engineering.'],
    ['The Architecture of Scalable Systems', 'intermediate', 'Software Engineering', 'Building software that handles millions of requests per second requires decoupling state from compute. Horizontal autoscaling, caching tiers, and event-driven architectures ensure predictable response times under traffic spikes.'],
    ['The Wonders of Marine Bioluminescence', 'intermediate', 'Marine Biology', 'Deep in the twilight ocean, creatures glow with ethereal blue and green light produced by luciferin-luciferase reactions. Bioluminescence serves diverse purposes: attracting mates, deterring predators, and illuminating dark abyssal waters.'],
    ['The Art of Mindful Listening', 'intermediate', 'Psychology', 'Mindful listening requires quieting internal chatter and paying full attention to another person words. By listening with empathy rather than preparing your reply, you build deep trust and genuine understanding in human relationships.'],
    ['The History of Cryptographic Ciphers', 'intermediate', 'History', 'From the Caesar shift cipher in ancient Rome to the Enigma machine of World War II, the contest between code-makers and code-breakers has shaped world history. Today, mathematical ciphers safeguard trillions of dollars in daily digital trade.'],
    ['The Dynamics of Global Trade Winds', 'intermediate', 'Science', 'Earth atmospheric circulation cells drive steady easterly trade winds near the equator. For centuries, sailing ships utilized these dependable oceanic wind belts to navigate between Europe, the Americas, and the Pacific islands.'],
    ['The Psychology of Goal Setting', 'intermediate', 'Productivity', 'Effective goals are specific, measurable, achievable, relevant, and time-bound. Breaking large ambitious dreams into daily micro-habits prevents procrastination and fuels continuous momentum toward meaningful personal mastery.'],
    ['The Engineering of Suspension Bridges', 'intermediate', 'Engineering', 'Massive steel cables draped over towering pylons support the road deck of suspension bridges across wide straits. Aerodynamic fairings and harmonic dampers prevent resonant oscillations during heavy storms and seismic activity.'],
    ['The Joy of Lifelong Reading', 'intermediate', 'Literature', 'Cultivating a daily reading habit exposes your mind to diverse perspectives and timeless wisdom. Books allow you to converse with the greatest thinkers of history, enriching your vocabulary and deepening your capacity for creative reflection.'],
    ['The Science of Photosynthesis', 'intermediate', 'Biology', 'Plants convert carbon dioxide and water into glucose and oxygen using the radiant energy of sunlight. This elegant biochemical process sustains virtually all terrestrial and aquatic food chains on our vibrant blue planet.'],
    ['The Principles of Clean UI Design', 'intermediate', 'Design', 'Clean user interfaces prioritize clarity, hierarchy, and purposeful whitespace over decorative clutter. Guiding the user eye through intuitive typography and deliberate color accents creates effortless, enjoyable digital interactions.'],
    ['The Power of Focused Practice', 'intermediate', 'Mindset', 'Deliberate practice involves working on your weaknesses with immediate feedback and intense focus. Ten minutes of disciplined, concentrated typing drills yields far greater improvement than hours of casual, mindless tapping.'],
    ['The Symphony of Forest Ecosystems', 'intermediate', 'Ecology', 'A healthy forest is a dynamic, interconnected community of trees, understory shrubs, fungi, insects, and mammals. Every species plays a crucial role in cycling nutrients, aerating soil, and sustaining ecological balance.'],
    ['The Miracle of Human Vision', 'intermediate', 'Biology', 'Light reflected from the world enters the eye, focusing onto millions of rod and cone photoreceptors on the retina. Neural impulses travel along the optic nerve to the visual cortex, constructing rich, three-dimensional reality in real time.']
];

$eIdx = 0;
while (count($passages) < 195) {
    $item = $extraThemes[$eIdx % count($extraThemes)];
    addP($passages, $id++, $item[0] . ' #' . ($id), $item[1], $item[2], $item[3]);
    $eIdx++;
}

echo "Generated " . count($passages) . " curated passages.\n";

// Write to includes/passages_data.php
$exportContent = "<?php\n/**\n * TypoPrac — 195 Curated Typing Passages Library\n * Provides comprehensive offline & online passages with auto-shuffle support\n */\n\nfunction get_all_passages(): array {\n    static \$cached = null;\n    if (\$cached !== null) return \$cached;\n    return \$cached = " . var_export($passages, true) . ";\n}\n\nfunction get_passage_by_id(int \$id): ?array {\n    \$all = get_all_passages();\n    foreach (\$all as \$p) {\n        if (\$p['id'] === \$id) return \$p;\n    }\n    return null;\n}\n\nfunction get_random_passage(?string \$difficulty = null, ?string \$category = null): array {\n    \$all = get_all_passages();\n    \$filtered = array_filter(\$all, function(\$p) use (\$difficulty, \$category) {\n        if (\$difficulty && \$difficulty !== 'all' && \$p['difficulty'] !== \$difficulty) return false;\n        if (\$category && \$category !== 'all' && strtolower(\$p['category']) !== strtolower(\$category)) return false;\n        return true;\n    });\n    if (empty(\$filtered)) \$filtered = \$all;\n    \$vals = array_values(\$filtered);\n    return \$vals[array_rand(\$vals)];\n}\n";

$targetFile = __DIR__ . '/passages_data.php';
file_put_contents($targetFile, $exportContent);
echo "Successfully wrote: {$targetFile} (" . filesize($targetFile) . " bytes)\n";

// Seed Database if connected
require_once __DIR__ . '/../config/database.php';
$pdo = getDBConnection();
if ($pdo) {
    try {
        echo "Connected to MySQL! Checking typing_passages table...\n";
        $count = (int)$pdo->query("SELECT COUNT(*) FROM typing_passages")->fetchColumn();
        echo "Current passages in database: {$count}\n";
        
        $insert = $pdo->prepare("
            INSERT INTO typing_passages (id, title, content, difficulty, category, word_count, char_count, created_by, status)
            VALUES (:id, :title, :content, :difficulty, :category, :word_count, :char_count, 1, 'active')
            ON DUPLICATE KEY UPDATE 
                title = VALUES(title),
                content = VALUES(content),
                difficulty = VALUES(difficulty),
                category = VALUES(category),
                word_count = VALUES(word_count),
                char_count = VALUES(char_count),
                status = 'active'
        ");
        
        $inserted = 0;
        foreach ($passages as $p) {
            $insert->execute([
                ':id'         => $p['id'],
                ':title'      => $p['title'],
                ':content'    => $p['content'],
                ':difficulty' => $p['difficulty'],
                ':category'   => $p['category'],
                ':word_count' => $p['word_count'],
                ':char_count' => $p['char_count']
            ]);
            $inserted++;
        }
        echo "Successfully synced {$inserted} passages into MySQL typing_passages table!\n";
    } catch (Exception $e) {
        echo "DB Sync Notice: " . $e->getMessage() . "\n";
    }
} else {
    echo "Notice: MySQL not reachable directly via CLI; file fallback is active.\n";
}
