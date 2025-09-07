<?php

namespace Database\Seeders;

use App\Models\Research;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user as the uploader (assuming user ID 2 exists)
        $uploader = User::find(2);
        if (!$uploader) {
            // If user 2 doesn't exist, create a default user or use the first available user
            $uploader = User::first();
            if (!$uploader) {
                $this->command->error('No users found. Please run UserSeeder first.');
                return;
            }
        }

        // Get the BSIT program
        $bsitProgram = Program::where('name', 'Bachelor of Science in Information Technology')->first();
        if (!$bsitProgram) {
            $this->command->error('BSIT program not found. Please run ProgramSeeder first.');
            return;
        }

        // Faculty email to ID mapping
        $facultyEmails = [
            'marvin@usep.edu.ph' => 7,
            'tammy@usep.edu.ph' => 10,
            'epricablanca@usep.edu.ph' => 26,
            'nancy.mozo@usep.edu.ph' => 12,
            'jamalkay.rogers@usep.edu.ph' => 16,
            'michaelanthony.jandayan@usep.edu.ph' => 27,
            'franch@usep.edu.ph' => 28,
            'cramante@usep.edu.ph' => 2,
            'hermoso.tupas@usep.edu.ph' => 19,
            'cedumdumaya@usep.edu.ph' => 4,
            'ariel.reyes@usep.edu.ph' => 15,
            'ikmachica@usep.edu.ph' => 8,
            'maui@usep.edu.ph' => 20,
            'val@usep.edu.ph' => 29,
        ];

        $researchData = [
            // 2020 Research
            [
                'title' => 'COPTURE: AUTOMATION OF TRAFFIC TICKET ISSUANCE USING PDF417 BARCODE SCANNER',
                'adviser_email' => 'marvin@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 5,
                'year' => 2020,
                'abstract' => 'Double encoding of traffic citations became a significant problem for the Apprehension Unit in the City Transport and Traffic Management Office. Every day, they have to encode the endorsed traffic tickets into an excel sheet, and they have even experienced a month\'s worth of backlog due to the increase in citation tickets. CopTure was developed to solve the agency\'s problem in double encoding and to enhance the process of issuing a traffic ticket. The researchers designed and developed a mobile application that will automate the issuance of traffic citations by scanning and obtaining data from a driver\'s license. They also developed a web-based record system that will allow authorized employees to monitor traffic citations. To achieve this, the researchers followed the Rapid Application Development method. The researchers conducted an interview at the CTTMO to thoroughly understand the problem and established a set of objectives to solve it. The objectives served as a guide in implementing all the features needed to complete the project. The mobile application and web-based system went through rapid prototyping and iterative delivery until all the objectives were met. A validation test was also conducted to ensure that both the application and the system are fully functioning. Overall, this project paints a picture of the future traffic ticketing system and encourages the acceptance of technology as a new way of implementing traffic management. The project would not be feasible without the unwavering commitment and cooperation that each of the researchers showed to successfully finish the project. The whole project might be finished and thrived, but it is still open for future improvements and additional features based on users\' future needs.',
            ],
            [
                'title' => 'FINnish Na: AN IOT APPLICATION SYSTEM FOR FISH MORTALITY RATE MONITORING USING ULTRASONIC SENSORS',
                'adviser_email' => 'tammy@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 5,
                'year' => 2020,
                'abstract' => 'Fish mortality is a natural occurrence that can happen when cultivating a fish farm. It is undeniable that fish deaths transpire now and then. Problems that contribute to fish mortality include weather that causes oxygen depletion, fish disease, as well as dead fish/es itself, among other things. These dead fishes, if not retrieved and left to rot, pose an even greater threat within a farm, especially when these carcasses sink instead of floating. If cultivators aren\'t careful enough, these rotting fish may release harmful chemicals that can contaminate the fishpond\'s water and compromise other healthy fishes. An interview was conducted among fish farmers in Matina Aplaya to find out how they address such issues. Collectively, they responded by having scheduled underwater checking for dead fish that sank at the bottom of the pond, which is time-consuming and inefficient. Hence, the proponents developed a system, FINnish Na, to reduce and address this specific fish farmer problem. Primarily using an ultrasonic sensor placed at the bottom of a basin, the proponents have simulated a miniature fishpond. When the sensor detects the presence of dead fish in the pond, the system will notify fish farmers through a notification in the app. The app can also provide the mortality rate of the fish and gives a daily and monthly report of the number of dead fish that the fish farm has so far. After testing the system for four (4) days, split between two pond conditions: with live fish and without, the proponents made comparisons of each day\'s result. It was evident that there was variability among the results. Significantly, however, there is a slight inconsistency of sensor readings when live fishes are present and if they are constantly moving. Based on the results of this study, the proponents recommend that an advanced type of ultrasonic sensor is utilized, as well as improve the sensor detection function, where constant interferences such as fish movement are ignored.',
            ],
            [
                'title' => 'CODE CAPTURE: MOBILE IDE FOR ENHANCING PROGRAMMING LOGIC BY CAPTURING PSEUDOCODES INTO READILY EXECUTABLE SCRIPTS USING OCR TECHNOLOGY',
                'adviser_email' => 'epricablanca@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 7,
                'year' => 2020,
                'abstract' => 'Laptops and smartphones are used by almost everyone in this current era. These devices are popularly used at home, school, and work environments. Students, in particular, prefer using laptops because they are more efficient to be used for notetaking, writing, editing, and studying. Having said that, several economically marginalized students may not experience the convenience that these devices could offer. This financial instability could be a big issue especially for technology-related students since laptops play a crucial role in learning the basics of computer programming. Therefore, the researchers have conducted this study, "Code Capture: Mobile IDE for Enhancing Programming Logic By Capturing Pseudocodes Into Readily Executable Scripts Using OCR Technology", a solution that could improve the current situation of students with financial difficulties of providing themselves laptops. This study created a dedicated mobile application to be used by students who have computer-related courses. It could serve as a compiler and decoder for computer programs. Following a Rapid Application Development (RAD) model, we used an effective and fair design to cater to the needs of different users.',
            ],
            [
                'title' => 'HEEDER: A VOIP-BASED HYBRID MOBILE APPLICATION FOR CLASSROOM INSTRUCTION DELIVERY',
                'adviser_email' => 'nancy.mozo@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 6,
                'year' => 2020,
                'abstract' => 'Two of the most common sources of distractions inside the classroom include noise and uncontrollable use of technology, specifically mobile devices, among students. Studies have shown that noise has a direct negative effect on student learning with language and reading development particularly affected. Moreover, technology is one of the factors which negatively affects the learning process of the students. The usage of mobile phones causes disturbance in the classroom affecting academic performances. However, due to the proliferating use of technology in classrooms, the researchers used this opportunity to utilize rather than restrict the students in using mobile devices as an effective tool for learning. Heeder is a mobile VOIP-enabled and hybrid voice distribution application that aims to provide an alternative tool for classroom instruction delivery. The purpose of this study was to provide convenience to learners who are easily distracted caused by noise and extensive use of mobile devices. Through the said application, teachers and students had the opportunity to better communicate with each other. The application established connections within users through creating channels, broadcasts real-time voice data, and monitors student users on the teachers\' side. Dynamic Systems Development Method was used to allow the researchers to create the application which requires flexible requirements in early phases. Upon fulfillment of this project, the proponents were able to develop a hybrid mobile application using Cordova framework that provides a tool for students in promoting learning through intent listening. For obvious reasons, network speed has an impact on the voice data quality of the application. Thus, the proponents have recommended creating an API that would not require the use of the internet for the reliability of voice data and localize the use of the application inside the campus. However, the application did not guarantee a total noise-free environment rather it enhanced the voice; thus, studies must consider eliminating the unpleasant noise especially in classroom settings.',
            ],
            [
                'title' => 'SMARTASTH: A MOBILE APPLICATION FOR REAL-TIME MONITORING OF ASTHMATIC PATIENTS USING WEARABLE DEVICE FOR HEART RATE AND GEO-TAGGING',
                'adviser_email' => 'jamalkay.rogers@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 6,
                'year' => 2020,
                'abstract' => 'Asthma is a lifetime chronic disease taking off to anomalous lung functions and difficulty in breathing. Asthma influences more than 300 million individuals around the world. Asthmatic patients have trouble breathing and airflow obstruction caused by inflammation and constriction of the airways. Home monitoring of lung function is the preferred course of action to give physicians and asthma patients a chance to control the disease jointly. Thus, it is important to develop accurate and efficient asthma monitoring devices that are easy for patients to use. \n\nObserving on our own is the preliminary course of action to monitor, treat, and control chronic disease. Self-checking mutually causes doctors and patients to have authority over ongoing observing and to give on-time treatment. A classical spirometry test is currently the preeminent way to diagnose the severity of lung functions and their response to treatment, but it requires supervision. Currently, portable devices are available to monitor Peak Expiratory Flow, but it is expensive and inconvenient to use. \n\nPrediction of severe exacerbation triggered by uncontrolled asthma is highly important for patients suffering from asthma, as avoiding maleficent symptoms that could need special treatment or even hospitalization, can protect patients from the aftereffects of bronchodilation. As of late, there has been an increased use of wireless sensor networks and embedded systems in the medical sector. Healthcare providers are now attempting to use these devices to monitor patients in a more accurate and automated way. This would permit healthcare providers to have up-to-date patient information without physical interaction, allowing for more accurate diagnoses and better treatment. \n\nIn this study, we present work in progress on an application scenario where a smartphone is used to detect and quantitatively index early signs of asthma attack triggers. Here, the embedded microphone in the smartphone records the user\'s breath sound while motion-sensor-heart rate changes. \n\nThis will overcome the shortcomings of the existing system by home monitoring the lung functions and patient\'s environmental parameters over time without any supervision as in standard spirometry tests. Our design and results show that using only built-in sensors in smartphones, mobile phones can sufficiently and reliably monitor the health status of patients with long-term respiratory conditions such as asthma.',
            ],
            [
                'title' => 'AEROFREE: AN IOT-ENABLED LPG LEAK DETECTION SYSTEM WITH PROXIMITY MAP',
                'adviser_email' => 'michaelanthony.jandayan@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 4,
                'year' => 2020,
                'abstract' => 'This study was conducted to prevent unnoticed gas leaks that might cause fire, increase awareness to the community, and help quicken the response time of the local fire department. The study drew attention to the fact that many fire incidents involving gas leaks resulted in massive explosions, human injuries, and even death. Furthermore, this research reveals that proper awareness, information, and prompt action are crucial to prevent such incidents. Aerofree app is a mobile application that uses an Arduino-based LPG leak sensor to help users detect dangerous levels of propane and butane gases. It notifies household owners and nearby households of a possible gas leak in the area. The Rapid Application Development (RAD) model was adopted, enabling early system integration and immediate troubleshooting. The application successfully activates actuators when LPG levels are high, sends alerts via SMS and app notifications, and provides a proximity heat map within a 100-meter radius of the device. This contributes to community safety and fire prevention.',
            ],
            [
                'title' => 'IMONGMOTHER: AN ANDROID-BASED COMMUNITY BREAST MILK SHARING APPLICATION USING GEOTAGGING AND CROWDSOURCING IN DAVAO CITY',
                'adviser_email' => 'marvin@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 5,
                'year' => 2020,
                'abstract' => 'In the Philippines, statistics have shown that many women are unable to exclusively breastfeed for six months due to insufficient breastmilk supply, while others produce excess. This study aims to connect these two groups through a mobile application that facilitates breastmilk sharing. The platform enables women with surplus milk to post donations, while those in need can request nearby donors using GPS filtering. The proponents utilized the Rapid Application Development (RAD) model to ensure timely system delivery and integration. The app fosters a breastfeeding culture, promotes maternal support, and helps alleviate postpartum challenges. The system met its objectives and opens opportunities for future improvements.',
            ],
            [
                'title' => 'CAREFUL: A MOBILE-BASED ROAD ALERT APPLICATION FOR ROAD SAFETY PRECAUTIONS USING GEOFENCING API',
                'adviser_email' => 'nancy.mozo@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 6,
                'year' => 2020,
                'abstract' => 'Road traffic accidents are rising due to increased vehicle usage and various human errors such as jaywalking, overspeeding, and distracted walking or driving. This project addresses pedestrian and driver safety through a mobile application that utilizes geofencing to send real-time alerts about nearby pedestrian lanes and accident-prone areas. It encourages safer behavior among both pedestrians and drivers. Drivers receive warnings near intersections, blind curves, and crowded zones, promoting speed control and attentiveness. The application contributes to road safety awareness and highlights the potential of mobile technology in minimizing accidents and improving public safety.',
            ],
            [
                'title' => 'TRAVIL: A MOBILE APPLICATION COMPLAINT TOOL FOR TRAFFIC VIOLATIONS AND INCIDENTS USING LIVE VIDEO FEED AND REAL-TIME LOCATION TRACKING',
                'adviser_email' => 'epricablanca@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 6,
                'year' => 2020,
                'abstract' => 'The rapidly increasing number of vehicles also raises traffic congestion that impacts the quality of life and productivity in every developing country. Aside from that, it also increases the number of traffic violations and traffic incidents. CCTVs and traffic enforcers are no longer enough to manage this underlying problem because there are too few traffic enforcers for the number of vehicles. The purpose of this study is to create a mobile-based application that will help traffic enforcers in dealing with traffic violations and incidents happening around Davao City. A mobile-based application was made that will serve as a complaint tool for traffic enforcers using live video feed and real-time location tracking. Live video feed was used to help the traffic enforcer to immediately validate the report and real-time location tracking to guide traffic enforcers to reach the location of the violator or the area of the incident. This application, implemented in the android platform using Visual Studio Code, Ionic Framework, and Java, allows the proponents to achieve the goal. Keywords: Traffic congestion, Traffic violations, Mobile-based application, Live video feed, Real-time location tracking, Traffic enforcement technology, Ionic Framework and Java',
            ],
            [
                'title' => 'LEARNDYS: AN EDUCATIONAL LEARNING APPLICATION FOR DYSLEXIC CHILDREN USING R.A.S.E. MODEL',
                'adviser_email' => 'marvin@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 12,
                'year' => 2020,
                'abstract' => 'One type of learning disability caused by a neurological disorder is dyslexia and the lack of intervention for dyslexic students is one of the major reasons why learners are frequently neglected and judged in society particularly by their peers. The importance of early intervention is vital. Although kids indeed learn in different ways and at different rates, it seems individuals with dyslexia are pretty much bom with special conditions in their brains. The earlier they receive an intervention, the higher the chance they may become better at learning words and reading. LearnDys was developed to help solve the problem of a lack of early intervention. The researchers designed and developed an educational learning application that provides cognitive and psychomotor activities intended for ages 3 to 6. The specific activities given by the application are only helpful for children with this condition. To achieve this, the researchers used the R.A.S.E. Model based on what is considered essential for ensuring quality in learning by using mobile applications to enhance the learning ability and ensure the entire achievement of the learning outcome. Based on the activities given, cognitive and psychomotor is part of the learning objective. Cognitive as the most common domain in learning that deals with the intellectual side, and psychomotor as a domain that focuses on motor skills and action requires physical coordination. The objective serves to complete and achieve the project and ensure that all the features must be present in the application. The researchers also seek help from the College of Education expert that handles children with this condition. Overall, this project would help the target users and address the problem with the help of technology. The project would not have been attainable without the researchers\' cooperation, hard work, and dedication. We hope that this project will improve more in the future and be able to deploy successfully. Keywords: Dyslexia intervention, Learning disability, Educational learning application, Cognitive and psychomotor activities, Early childhood education, R.A.S.E. Model, Mobile learning technology',
            ],
            [
                'title' => 'PACOOL: A WEARABLE DEVICE PROVIDING COOLING EFFECT TO PREVENT HEAT-RELATED ILLNESSES USING PELTIER MODULE',
                'adviser_email' => 'epricablanca@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 6,
                'year' => 2020,
                'abstract' => 'An extreme heat wave is dangerous to people who are exposed directly to the heat, especially to elderly people who slowly absorb heat in the body that may lead to heatstroke. According to the World Health Organization, 70,000 people died in Europe because of the June-August event in 2003 and in 2010, 56,000 excess deaths occurred during a 44-day heat wave in the Russian Federation. Heat exhaustion may lead to heatstroke. If one has symptoms of heat exhaustion, it is necessary to get inside or find a cool shady place to cool down. Prevention is always better than cure. The fastest and most effective way of alleviating heatstroke or heat exhaustion is cooling the whole body. With the primary solution in preventing heatstroke, PaCool aims to develop a device that provides a cooling effect to help the users cool their bodies whenever their body temperature increases above normal body temperatures caused by the heat waves and a real-time mobile application that lets the user monitor their body temperature from time to time. The device of this project is attached in the wrist and the uppermost of the arm near the armpit where the temperature sensor can detect the body temperature of the user. The device located in the wrist releases a cooling sensation that enables to cool the whole body. The researchers used the Peltier module to produce cooling effects. Keywords: Heatwave prevention, Heatstroke and heat exhaustion, Body temperature monitoring, Wearable cooling device, Real-time mobile application, Peltier module technology, Heat protection',
            ],
            [
                'title' => 'COPIoT: A Web Based Monitoring System for Automated Copra Drying Process',
                'adviser_email' => 'franch@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 6,
                'year' => 2020,
                'abstract' => 'Copra is produced using sun drying or smoke methods traditionally done by small-scale coconut farmers, and both methods of drying have adverse effects on the quality of copra. The study aims to aid the copra industry by producing an artificial drying machine for copra, to automate the drying process and gather real-time data. The web-based monitoring system integrated with the drying machine visualizes the drying process of copra, which guarantees the quality of copra produced by small-scale coconut farmers.',
            ],

            // 2021 Research
            [
                'title' => 'lsdaCulture An IoT - Based Water Temperature and Dissolved Oxygen Level Monitoring System for Milkfish Farming',
                'adviser_email' => 'ikmachica@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 8,
                'year' => 2021,
                'abstract' => 'In aquaculture, the main cause of fish mortality is an increase in water temperature that causes oxygen loss. The amount of dissolved oxygen in the water reduces as the temperature rises. The research aimed to design and develop an improved smart fish pond monitoring system for milkfish farming. It will notify the farmers whenever the water temperature or dissolved oxygen levels change. The system was designed to monitor pond water using temperature and dissolved oxygen level sensors. Sensors send pre-processed data to a server through the built-in WIFI module of the microcontroller. The mobile application generates significant pond parameters to generate significant parameters and activates actuators to maintain water temperature. The researchers used the Rapid Application Development (RAD) methodology as the development model to keep track of progress and give real-time updates on any problems or modifications that emerge. The devices used in this research were two sensors, three actuators, a mobile application, and a server. The testing process includes using pre-processed data that was sent to a local server. The server analyzes water temperature and dissolved oxygen levels. The app then displays the data in various places of the app once it has been processed. It also includes a series of conditional statements to identify the pond\'s state. The sensors detect changes in water temperature and alert the owner or caretaker by sending push notifications about temperature fluctuations after gathering and processing the data. It uses a microcontroller to analyze a sequence of conditional statements before activating the specific actuators. The study concluded that the microcontroller generated important parameters and data logs that indicated the pond and fish condition. A push notification was sent to the smartphone, informing it of the current state of the pond in real-time. The actuators will automatically turn on and off after regulating the water temperatures and dissolved oxygen levels.',
            ],
            [
                'title' => 'UVwearloT: AN IoT BASED WEARABLE DEVICE COMPOSE OF TWO SMART SENSORS TO MONITOR ULTRAVIOLET INDEX (UVI) LEVEL (UV SENSOR) AND PULSE RATE MONITORING (PULSE SENSOR) TO TRACKDOWN ACTIVITIES',
                'adviser_email' => 'hermoso.tupas@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 6,
                'year' => 2021,
                'abstract' => 'There are two types of UV light that are proven to contribute to the risk for skin cancer: Ultraviolet A (UVA) and Ultraviolet B (UVB). This study developed a wearable device with UV and pulse rate sensors connected to a mobile app to notify the user about UV radiation risks and abnormal pulse rate. The system was developed using Android Studio and Arduino IDE for real-time monitoring and notifications for user safety.',
            ],
            [
                'title' => 'EMPATHYVR: A LEARNING COMMUNICATION PLATFORM FOR CHILDREN WITH AUTISM',
                'adviser_email' => 'hermoso.tupas@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 1,
                'year' => 2021,
                'abstract' => 'This research aimed to develop an assistive technology using virtual reality to improve the communication skills of children with autism. The system is designed as a game-based learning platform that helps users progress through levels to enhance communication abilities, with data monitored to track their development. Despite the challenges posed by the COVID-19 pandemic, the study showed that virtual reality can be an effective method for autistic children.',
            ],
            [
                'title' => 'SOS\'IoT: A Noise Monitoring and Warning Tool for Barangay',
                'adviser_email' => 'maui@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 6,
                'year' => 2021,
                'abstract' => 'This study developed a noise monitoring system to help barangay officials promote peace and order by monitoring noise levels in their area. The system utilized the Rapid Application Development (RAD) methodology, and despite challenges due to the COVID-19 pandemic, the researchers developed a noise detection simulator using an ESP8266 and KY-038 microphone. The mobile app developed showed the retrieved data for analysis.',
            ],
            [
                'title' => 'RedPing: An IoT-Based Flood Detection System for Urban Areas',
                'adviser_email' => 'hermoso.tupas@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 10,
                'year' => 2021,
                'abstract' => 'Flooding is an imminent phenomenon mostly in equatorial regions. Risk and challenges occur from flooding when it involves endangering lives and damage to properties. Traditional studies and solutions to flooding often involve monitoring inland bodies of water such as rivers, dams, and lowly elevated areas. However, there is little attention given to street flooding, its effects on transportation, and its solutions. The paper investigated the effects of street flooding in transportation and the current solutions available. RedPing is an IoT-based solution to monitor street floods using sonar technology housed in a pole structure to measure flood levels. Data is sent to a server that serves as the database for the application.',
            ],
            [
                'title' => 'IoTae: A Web Based Monitoring System for Harmful Algal Bloom Growth in Ponds Using Water Temperature, Ph and Dissolved Oxygen Sensors',
                'adviser_email' => 'ariel.reyes@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 12,
                'year' => 2021,
                'abstract' => 'The proposed project IoTae is a web-based system that monitors the presence of harmful algal bloom growth in ponds using temperature, pH and dissolved oxygen (DO) sensors. The project aims to raise awareness and provide early warnings to pond owners, government organizations, and LGUs to take actions before it becomes critical. The system provides notifications when the indicators reach or exceed the optimum level, triggering the aeration process.',
            ],
            [
                'title' => 'Project T-RAT: An IoT Based Smart-Trapper for Rats',
                'adviser_email' => 'ariel.reyes@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 12,
                'year' => 2021,
                'abstract' => 'The project T-RAT was developed to help household and business owners capture rats in their establishments. The device uses sensors (weight sensor, infrared sensor, and camera) to ensure accurate rat capture. The system notifies users through a mobile application when a rat is captured. The system underwent several phases of development, including testing the accuracy of the device, notification speed, and safety features for handling the trap.',
            ],
            [
                'title' => 'HAPPAG: A MOBILE APPLICATION CONNECTING FOOD DONORS AND DONEES TO PREVENT FOOD WASTES',
                'adviser_email' => 'val@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 6,
                'year' => 2021,
                'abstract' => 'The following was a proposal for a mobile application that helps prevent food wastage. The goal was to help reduce food wastes being thrown into landfills that would cause serious issues to the environment such as climate change. Generally, this application helps connect food-related establishments, charitable organizations, and food composting facilities by allowing food-related establishments such as restaurants, supermarkets, cafeterias, and fast-food chains to donate their food waste and encourage them not to throw away food. Additionally, to make use of inedible food waste that can be used for composting. The application had undergone four phases of development using an agile software development methodology due to the amount of time given for the completion of this project. The proponents focused on prototype iterations with less planning time and measured progress and communicated real-time on evolving issues. The application displays a geographical representation of nearby donors and recipients that matches their needs using Google Map API. Furthermore, the proponents used a library called Socket.IO that was used for in-app messaging, allowing both users to interact or communicate real-time through the mobile application and react native chart kits plugin to display through visualization the donated food wastes to provide donors a decision support by analyzing the data to help them assess the reduction or increase of their food wastes. The project aimed to raise awareness of the impacts of food wastes and encouraged food-related establishments, including households to avoid throwing edible foods considering that there are some people who don\'t have enough food on their table. After thorough research and development, the objectives of the project were met, and results showed that the application developed was able to help donors donate their food wastes to recipients easily and conveniently, leading towards food waste prevention.',
            ],

            // 2022 Research
            [
                'title' => 'E-MONGANI: A Mobile Application for Marketing Rice Through a Bidding System',
                'adviser_email' => 'marvin@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 6,
                'year' => 2022,
                'abstract' => 'This research presents an e-commerce mobile application for palay marketing that bridges the gap between local farmers and buyers. It includes a bidding feature that allows farmers to set a minimum price for their palay, helping them to accumulate fair value for their product. The system provides a strategy for rice farmers to sell their product directly to buyers, circumventing middlemen, and ensuring higher profit margins.',
            ],
            [
                'title' => 'DamageXpert: A Mobile-Based Application for the Identification of Damages Caused by Rice Leaf Blast and Rice Stem Borer with Control Measures',
                'adviser_email' => 'michaelanthony.jandayan@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 7,
                'year' => 2022,
                'abstract' => 'The DamageXpert mobile application helps farmers detect and identify damages caused by rice leaf blast and rice stem borer. It aids farmers in differentiating the symptoms and managing these infestations through Integrated Pest Management (IPM). This tool provides a practical and efficient solution to minimize crop damage and improve rice yield by offering accurate pest control measures.',
            ],
            [
                'title' => 'QualitAire: An IoT-Based Air Quality Monitoring System with Forecasting Capability Through Time Series Model Analysis',
                'adviser_email' => 'cramante@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 6,
                'year' => 2022,
                'abstract' => 'Air pollution is defined as contamination of the indoor or outdoor environment by any chemical, physical, or biological agent that alters the inherent properties of the atmosphere. Particulate matter, carbon monoxide, ozone, nitrogen dioxide, and sulfur dioxide are all serious public health concerns. Air pollution, both indoor and outdoor, causes respiratory and other illnesses and is a major cause of morbidity and mortality. This study aims to create a system with forecasting capability by constantly updating the current air concentrations and predicting the next numbers of the Air Quality Index or AQI. This study intends to make practical and effective use of the Internet of Things (IoT) concept. This study consists of a device with sensors, namely, DSM501A PM Sensor, MQ7 CO Sensor, and MQ131 03 Sensor. A mobile-responsive web application and a cloud database. These sensors will be connected to the Arduino UNO microcontroller and then to the NODEMCU WiFi module. The microcontroller will send the data from the sensors to the cloud database. The data stored on the cloud database can be viewed on the mobile-responsive web application. This provides accurate time information on the actual air concentrations and their AQI, along with a table of the latest data of its previous readings. As shown in the line graph, the prediction feature can be observed at every 30-minute interval. Using the Time Series Model, particularly the ARIMA model of getting prediction. Lastly, the system has an archive feature so that all the data sent by the device can be seen for future reference.',
            ],
            [
                'title' => 'DESIGN AND DEVELOPMENT OF A MOBILE-BASED MALICIOUS URL DETECTION APPLICATION',
                'adviser_email' => 'hermoso.tupas@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 6,
                'year' => 2022,
                'abstract' => 'Year over year, communication tools such as social media are highly targeted by cybercriminals. Their schemes include the distribution of malicious URLs. A malicious URL is a URL that facilitates scams, frauds, and a cyberattack. By clicking on a malicious URL, a person can automatically download a malware program or a virus that can take over their devices or trick them into disclosing sensitive information on a fake website. End users who lack a fundamental understanding of information security are easier to be exploited by cybercriminals. One of the solutions for this kind of problem is using blocklist lookup; though effective, blocklists have a significant false-negative rate and lack the ability to detect newly generated malicious URLs. To address this problem, the researchers developed a mobile application called Mal Where, which can detect website links from image data. MalWhere also utilizes and combines the two known URL classification approaches: blocklisting and machine learning. With the blocklist service, MalWhere has access to a large number of Malicious URLs around the world. With machine learning, MalWhere can predict the classification of a URL, whether it is benign or malicious. The classification model was trained using 39 features and a supervised machine learning classifier called XGBoost. The classification ability of the model is mainly used to classify unknown and benign URLs to the blocklist service. Based on the conducted testing, MalWhere has an 88% accuracy rate in predicting the classification of a URL-whether it is benign or malicious.',
            ],
            [
                'title' => 'STUDYMATE: A STUDY PEER RECOMMENDER APP USING RECIPROCAL RECOMMENDATION ALGORITHM',
                'adviser_email' => 'cedumdumaya@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 6,
                'year' => 2022,
                'abstract' => 'When organizing a study group, finding and selecting a good study partner is essential because it increases the relevance and productivity of group discussions. Most of the time, students form study groups with peers with similar characteristics and interests. However, finding a suitable study mate takes time and effort. This work focused on creating a study peer recommender system that uses a reciprocal recommendation algorithm to help students find like-minded study partners and foster informal learning communities among students. The peer recommendation approach uses student traits, communicative openness, and Personality as matching factors. The modified waterfall model was utilized as the core methodology to implement the system for its flexibility in requirement evolution and iteration, which helped the proponents deliver the project on schedule.',
            ],
            [
                'title' => 'STRESSSENSE: A STRESS LEVEL DETECTOR FOR DETERMINATION OF STRESS LEVEL THROUGH THE COMBINATION OF PHYSIOLOGICAL DATA OF GALVANIC SKIN RESPONSE AND PULSE RATE',
                'adviser_email' => 'ariel.reyes@usep.edu.ph',
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 2,
                'year' => 2022,
                'abstract' => 'Stress is generally experienced in our daily lives. It is also inescapable. Stress is a response to a particular event or situation. It is the way that our body prepares to face difficult situations which require focus, strength, and heightened alertness. If an individual experiences stress, the body will react to respond to the causes or factors of stress. The body responds through sweat glands which produce electrical flow (conductance) and pulse rate as well. In this paper, a device was developed to be able to determine the stress level of an individual, particularly for the tertiary students at the University of Southeastern Philippines. Galvanic Skin Response (GSR) detects strong emotions and electrical flow through the skin and the pulse sensor detects the fluctuation of the blood pumped by the human heart (beats-per-minute/BMP). The gathered data will be interpreted to its corresponding level based on the stress parameters and will provide the final stress level output based on the table formulated from the fuzzy logic method. The testing was conducted alongside the ten (10) respondents; five (5) male and five (5) female students at the said university and their final stress level was displayed through the serial monitor of Arduino.',
            ],
            [
                'title' => 'ATONGSECRET: A WEB-BASED FILE SHARING AND MESSAGING APPLICATION USING IMAGE STEGANOGRAPHY',
                'adviser_email' => null, // This one has NULL adviser
                'program_name' => 'Bachelor of Science in Information Technology',
                'month' => 6,
                'year' => 2022,
                'abstract' => 'Steganography is concealing user data in various file types, such as photographs. The primary goal of steganography is to conceal private data; therefore, it should be treated with care. The security of Steganography is based on the invisibility of secret information in the stego picture, allowing the information to remain undetectable. The researchers developed a web application using Least Significant Bit Steganography in this capstone project. The project results showed that the web app successfully sent messages, concealed files in images, verified the receiver with a stego key, and displayed push notifications for received messages and files. The researchers used PHP, CSS, HTML, MYSQL, AJAX, JQUERY, and Hostinger web hosting to test and develop the web application. The application developed in this project became a medium where users safely send messages, was able to conceal files, deliver the stego key to the intended receiver and showed push notifications to its users.',
            ],
        ];

        foreach ($researchData as $data) {
            // Find the faculty adviser by email (skip if adviser is null)
            $adviser = null;
            if ($data['adviser_email']) {
                $adviser = Faculty::where('email', $data['adviser_email'])->first();
                if (!$adviser) {
                    $this->command->warn("Faculty with email {$data['adviser_email']} not found. Skipping research: {$data['title']}");
                    continue;
                }
            }

            // Find the program by name
            $program = Program::where('name', $data['program_name'])->first();
            if (!$program) {
                $this->command->warn("Program {$data['program_name']} not found. Skipping research: {$data['title']}");
                continue;
            }

            // Create the research entry
            Research::create([
                'uploaded_by' => $uploader->id,
                'research_title' => $data['title'],
                'research_adviser' => $adviser ? $adviser->id : null,
                'program_id' => $program->id,
                'published_month' => $data['month'],
                'published_year' => $data['year'],
                'research_abstract' => $data['abstract'],
                'research_approval_sheet' => null,
                'research_manuscript' => null,
            ]);
        }

        $this->command->info('Research data seeded successfully!');
    }
}
