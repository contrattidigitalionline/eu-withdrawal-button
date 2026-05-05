=== EU Withdrawal Button ===
Contributors: yourname
Tags: woocommerce, withdrawal, consumer rights, EU directive, recesso
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
WC requires at least: 7.0
Stable tag: 1.0.0
License: GPL-2.0+

Implements the mandatory EU withdrawal button as required by Directive (EU) 2023/2673, effective 19 June 2026.

== Description ==

This plugin adds a fully compliant electronic withdrawal function to your WooCommerce store, meeting the requirements of:

* **Directive (EU) 2023/2673** (amending Consumer Rights Directive 2011/83/EU)
* Effective from **19 June 2026** across all EU member states

=== Features ===

* Two-step withdrawal flow ("Recedi dal contratto qui" → "Conferma recesso qui") as required by the directive
* 14-day withdrawal window (configurable)
* Full audit log of all withdrawal requests in the WP admin
* Automatic email confirmation sent to the customer
* Automatic notification sent to the shop admin
* WooCommerce order status updated automatically after confirmed withdrawal
* Italian language included; fully translatable via .po/.mo files
* GDPR-friendly: collects only the minimum necessary data
* Works with standard WooCommerce (no page builder required)

== Installation ==

1. Upload the `eu-withdrawal-button` folder to `/wp-content/plugins/`
2. Activate the plugin through **Plugins > Installed Plugins**
3. Go to **EU Withdrawal > Impostazioni** to configure the admin notification email
4. The withdrawal button will automatically appear on the WooCommerce **View Order** page for all orders within the withdrawal window

== Configuration ==

Navigate to **EU Withdrawal > Impostazioni** in your WordPress admin:

* **Withdrawal window (days)** — default 14, minimum required by the directive
* **Admin notification email** — where withdrawal notifications are sent

== How it works ==

1. Customer visits their order page
2. If within the 14-day window, they see the withdrawal section with a form
3. They fill in name, email, and optional reason, then click **"Recedi dal contratto qui"**
4. A second confirmation screen appears with the button **"Conferma recesso qui"**
5. On confirmation: the withdrawal is logged, the order status is updated, and both the customer and admin receive an email

== Compliance notes ==

The two-step flow (intent + confirmation) mirrors the German "2-click Widerrufsbutton" model (§ 312k BGB) and satisfies the requirements of Art. 11a of the Consumer Rights Directive as amended by Directive (EU) 2023/2673.

**This plugin does not constitute legal advice.** You should verify compliance requirements with a qualified lawyer in each EU member state where you operate.

== Changelog ==

= 1.0.0 =
* Initial release

== Frequently Asked Questions ==

= Does this work without WooCommerce? =
No. This plugin requires WooCommerce to function.

= Does it work for guest orders? =
Yes. Guest customers can also access the withdrawal function via the standard WooCommerce order-received / view-order pages.

= Can I change the 14-day window? =
Yes, via the settings page — but the EU directive sets 14 days as the legal minimum. Only increase it, never decrease below 14.
