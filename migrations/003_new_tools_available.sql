-- Restaurant Ordering, Hotel Booking, Lessons Manager: διαθέσιμα με ενδεικτικές τιμές
-- (αλλάζουν από Διαχείριση → Εργαλεία & τιμές)

UPDATE tools SET status = 'available', features = 'Δική σου σελίδα παραγγελιών χωρίς προμήθεια\nDelivery και take away\nΟθόνη παραγγελιών με ήχο\nΕκτύπωση για την κουζίνα'
WHERE slug = 'restaurant-ordering' AND status = 'soon';
UPDATE tools SET status = 'available', features = 'Μηχανή κρατήσεων για το site σου\nΤιμές ανά περίοδο\nΚλείσιμο ημερών από άλλες πλατφόρμες\nEmail επιβεβαίωσης στον επισκέπτη'
WHERE slug = 'hotel-booking' AND status = 'soon';
UPDATE tools SET status = 'available', features = 'Τμήματα και πρόγραμμα\nΠαρουσίες με ένα πάτημα\nΔίδακτρα και οφειλές\nΣύνδεσμος για τους γονείς'
WHERE slug = 'lessons' AND status = 'soon';

INSERT INTO plans (tool_id, name, summary, price_cents, period, popular, sort)
SELECT t.id, 'Standard', 'Απεριόριστες παραγγελίες · 0% προμήθεια', 2900, 'month', 1, 1 FROM tools t WHERE t.slug = 'restaurant-ordering'
  AND NOT EXISTS (SELECT 1 FROM plans x WHERE x.tool_id = t.id);
INSERT INTO plans (tool_id, name, summary, price_cents, period, popular, sort)
SELECT t.id, 'Standard', 'Έως 20 δωμάτια · απευθείας κρατήσεις', 3900, 'month', 1, 1 FROM tools t WHERE t.slug = 'hotel-booking'
  AND NOT EXISTS (SELECT 1 FROM plans x WHERE x.tool_id = t.id);
INSERT INTO plans (tool_id, name, summary, price_cents, period, popular, sort)
SELECT t.id, 'Standard', 'Απεριόριστοι μαθητές · παρουσίες · δίδακτρα', 1900, 'month', 1, 1 FROM tools t WHERE t.slug = 'lessons'
  AND NOT EXISTS (SELECT 1 FROM plans x WHERE x.tool_id = t.id);
