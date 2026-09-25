-- Αρχικός κατάλογος εργαλείων και πλάνων.
-- Τιμές, περιγραφές και κατάσταση αλλάζουν από Διαχείριση → Εργαλεία.

INSERT INTO settings (k, v) VALUES
('company_name', 'MyMedia'),
('bank_name', 'Τράπεζα'),
('bank_iban', 'GR00 0000 0000 0000 0000 0000 000'),
('bank_beneficiary', 'MyMedia'),
('invoice_due_days', '7'),
('support_email', '');

INSERT INTO tools (slug, name, tagline, short, description, audience, features, icon, color, status, sort) VALUES
('qr-boss', 'QR Boss', 'Κάθε τραπέζι, βιτρίνα και έντυπο γίνεται ψηφιακή εμπειρία.', 'Εμπειρίες QR',
 'Δυναμικά QR για μενού, προσφορές, Wi-Fi και social, που αλλάζουν περιεχόμενο χωρίς να ξανατυπωθούν.',
 'Εστίαση, καφέ, λιανική, εκδηλώσεις',
 'Δυναμικά QR που αλλάζουν χωρίς επανεκτύπωση\nΨηφιακό μενού με φωτογραφίες\nΣτατιστικά σαρώσεων\nΚλήση σερβιτόρου (Pro)',
 'qr', 'purple', 'available', 10),
('review-booster', 'Review Booster', 'Μετατρέψτε τους ευχαριστημένους πελάτες σε ορατή απόδειξη.', 'Διαχείριση φήμης',
 'Ζητήστε feedback την κατάλληλη στιγμή, κάντε τις δημόσιες αξιολογήσεις εύκολες και δώστε στους δυσαρεστημένους πελάτες έναν ιδιωτικό τρόπο να μιλήσουν.',
 'Εστίαση, λιανική, υγεία, ομορφιά και υπηρεσίες',
 'QR αξιολόγησης και smart link\nΙδιωτική διαδρομή feedback\nDashboard ανά τοποθεσία\nΕβδομαδιαία αναφορά φήμης',
 'star', 'orange', 'available', 20),
('orderflow', 'OrderFlow', 'Παραγγελίες σε προμηθευτές χωρίς τηλέφωνα και χαρτάκια.', 'Παραγγελίες σε προμηθευτές',
 'Λίστες ειδών ανά προμηθευτή, παραγγελία με ένα πάτημα από το κινητό και ιστορικό για να ξέρεις τι παρήγγειλες και πότε.',
 'Εστίαση, καφέ, μικρά καταστήματα',
 'Κατάλογος ειδών ανά προμηθευτή\nΠαραγγελία μέσω email ή Viber\nΙστορικό και σύνοψη εξόδων\nΠολλαπλοί χρήστες',
 'truck', 'indigo', 'available', 30),
('tameio', 'Ταμείο', 'Το ημερήσιο κλείσιμο ταμείου, οργανωμένο.', 'Κλείσιμο ταμείου & έξοδα',
 'Καταγραφή εσόδων και εξόδων της ημέρας, κλείσιμο βάρδιας και αναφορές ανά εβδομάδα και μήνα.',
 'Κάθε επιχείρηση με ταμείο',
 'Ημερήσιο κλείσιμο ανά βάρδια\nΚαταγραφή εξόδων με φωτογραφία απόδειξης\nΑναφορές εβδομάδας και μήνα\nΔικαιώματα ανά χρήστη',
 'wallet', 'teal', 'available', 40),
('shifts', 'Βάρδιες', 'Το πρόγραμμα της ομάδας σου σε ένα σημείο.', 'Πρόγραμμα βαρδιών',
 'Φτιάξε το εβδομαδιαίο πρόγραμμα, μοιράσου το με την ομάδα και δέξου αιτήματα αλλαγής από το κινητό.',
 'Εστίαση, λιανική, υπηρεσίες με προσωπικό',
 'Εβδομαδιαίο πρόγραμμα με drag & drop\nΕιδοποίηση στην ομάδα\nΑιτήματα αλλαγής και άδειας\nΣύνοψη ωρών ανά άτομο',
 'calendar', 'blue', 'available', 50),
('appointments', 'Appointments', 'Κρατήσεις ραντεβού 24/7, χωρίς τηλέφωνα.', 'Ραντεβού για κομμωτήρια & barber',
 'Online κράτηση ραντεβού ανά υπηρεσία και συνεργάτη, με υπενθυμίσεις για λιγότερα no-show.',
 'Κομμωτήρια, barber, ομορφιά, υγεία',
 'Σελίδα κρατήσεων για τους πελάτες\nΗμερολόγιο ανά συνεργάτη\nΥπενθυμίσεις με SMS ή email\nΙστορικό πελάτη',
 'scissors', 'pink', 'available', 60),
('websites', 'Websites', 'Επαγγελματικό site, φτιαγμένο και συντηρημένο για εσένα.', 'Κατασκευή & συντήρηση site',
 'Σχεδιάζουμε, φιλοξενούμε και ενημερώνουμε το site της επιχείρησής σου, ώστε να μην ασχολείσαι εσύ.',
 'Κάθε επιχείρηση',
 'Σχεδιασμός στα μέτρα σου\nΦιλοξενία και SSL\nΜηνιαίες αλλαγές περιεχομένου\nΒασικό SEO και Google Business',
 'globe', 'blue', 'available', 70),
('ai-content', 'AI Content', 'Posts και κείμενα για τα social σου, κάθε εβδομάδα.', 'Περιεχόμενο με AI',
 'Προτάσεις για posts, λεζάντες και προσφορές προσαρμοσμένες στην επιχείρησή σου, έτοιμες για δημοσίευση.',
 'Κάθε επιχείρηση με social media',
 'Εβδομαδιαίο πλάνο posts\nΛεζάντες και hashtags\nΙδέες προσφορών\nΣτο ύφος της επιχείρησής σου',
 'logo', 'purple', 'available', 80),
('restaurant-ordering', 'Restaurant Ordering', 'Online παραγγελίες απευθείας, χωρίς προμήθειες.', 'Απευθείας online παραγγελίες',
 'Δική σου σελίδα παραγγελιών για delivery και take away, χωρίς προμήθεια ανά παραγγελία.',
 'Εστίαση', NULL, 'utensils', 'pink', 'soon', 90),
('hotel-booking', 'Hotel Booking', 'Απευθείας κρατήσεις από το site σου.', 'Απευθείας κρατήσεις',
 'Μηχανή κρατήσεων για το site του καταλύματος, χωρίς προμήθειες σε πλατφόρμες.',
 'Ξενοδοχεία, ενοικιαζόμενα', NULL, 'building', 'green', 'soon', 100),
('lessons', 'Lessons Manager', 'Μαθήματα, τμήματα και πληρωμές οργανωμένα.', 'Διαχείριση εκπαίδευσης',
 'Τμήματα, παρουσίες, δίδακτρα και επικοινωνία με γονείς ή μαθητές σε ένα μέρος.',
 'Φροντιστήρια, σχολές, γυμναστήρια', NULL, 'book', 'indigo', 'soon', 110);

INSERT INTO plans (tool_id, name, summary, price_cents, period, popular, sort)
SELECT id, 'Standard', '1 τοποθεσία · δυναμικά QR · ψηφιακό μενού', 1900, 'month', 0, 1 FROM tools WHERE slug = 'qr-boss';
INSERT INTO plans (tool_id, name, summary, price_cents, period, popular, sort)
SELECT id, 'Pro', 'Έως 3 τοποθεσίες · κλήση σερβιτόρου · στατιστικά', 3900, 'month', 1, 2 FROM tools WHERE slug = 'qr-boss';

INSERT INTO plans (tool_id, name, summary, price_cents, period, popular, sort)
SELECT id, 'Standard', '1 τοποθεσία · QR & smart link · αναφορά φήμης', 2400, 'month', 0, 1 FROM tools WHERE slug = 'review-booster';
INSERT INTO plans (tool_id, name, summary, price_cents, period, popular, sort)
SELECT id, 'Pro', 'Έως 3 τοποθεσίες · AI απαντήσεις · dashboard', 3900, 'month', 1, 2 FROM tools WHERE slug = 'review-booster';

INSERT INTO plans (tool_id, name, summary, price_cents, period, popular, sort)
SELECT id, 'Standard', 'Απεριόριστοι προμηθευτές · 3 χρήστες', 1900, 'month', 1, 1 FROM tools WHERE slug = 'orderflow';

INSERT INTO plans (tool_id, name, summary, price_cents, period, popular, sort)
SELECT id, 'Standard', '1 ταμείο · απεριόριστες βάρδιες · αναφορές', 1900, 'month', 1, 1 FROM tools WHERE slug = 'tameio';

INSERT INTO plans (tool_id, name, summary, price_cents, period, popular, sort)
SELECT id, 'Standard', 'Έως 15 άτομα · αιτήματα αλλαγών', 1500, 'month', 1, 1 FROM tools WHERE slug = 'shifts';

INSERT INTO plans (tool_id, name, summary, price_cents, period, popular, sort)
SELECT id, 'Ετήσιο', 'Απεριόριστα ραντεβού · υπενθυμίσεις', 35000, 'year', 1, 1 FROM tools WHERE slug = 'appointments';

INSERT INTO plans (tool_id, name, summary, price_cents, period, popular, sort)
SELECT id, 'Care', 'Φιλοξενία · SSL · μηνιαίες αλλαγές', 2500, 'month', 1, 1 FROM tools WHERE slug = 'websites';

INSERT INTO plans (tool_id, name, summary, price_cents, period, popular, sort)
SELECT id, 'Standard', '12 posts τον μήνα · λεζάντες · ιδέες', 2900, 'month', 1, 1 FROM tools WHERE slug = 'ai-content';
