<?php

namespace GiftGroup\GeoPage\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private $categoryNameMappings = [];

    private $categoryUrlKeyMappings = [];

    public const STATE_PAGE_URL = 'city/%s/gift-baskets';

    public const CITY_PAGE_URL = 'city/%s/%s/gift-baskets';

    public const CITY_CATEGORY_PAGE_URL = 'city/%s/%s/gift-basket/%s';

    public const CITY_PAGE_BASE_PATH = 'geopage/city/view/';

    public const CITY_PAGE_TARGET_URL = self::CITY_PAGE_BASE_PATH . 'id/%s';

    public const CITY_CATEGORY_PAGE_BASE_PATH = 'geopage/citycategory/view/';

    public const CITY_CATEGORY_PAGE_TARGET_URL = self::CITY_CATEGORY_PAGE_BASE_PATH . 'id/%s';

    public const STATE_PAGE_BASE_PATH = 'geopage/state/view/';

    public const STATE_PAGE_TARGET_URL = self::STATE_PAGE_BASE_PATH . 'id/%s';

    public const CITY_HUB_PAGE_URL = 'city';

    public const CITY_HUB_PAGE_TARGET_URL = 'geopage/city/index';

    public const PATTERN_FOR_URLREWRITE_OF_CITY_AND_CATEGORY = 'city/%s/%s/gift-basket/%s';

    public const CITY_PAGE_DEFAULT_PRODUCT_LIST_LIMIT = 16;

    public const CITY_PAGE_DEFAULT_REVIEW_PRODUCT_LIMIT = 10;

    public const CITY_PAGE_DEFAULT_POPULAR_PRODUCT_LIMIT = 6;

    public const CITY_PAGE_DEFAULT_CATEGORY_PAGE_LIST_LIMIT = 18;

    public const RELATED_CITY_LIST_DEFAULT_LIMIT = 20;

    public const PRODUCT_REVIEW_LIST_DEFAULT_LIMIT = 10;

    private const XML_PATH_EXTERNAL_LINKS = 'geo_page/additonal_script_css/external_links';

    private const XML_PATH_ADDITONAL_STYLES = 'geo_page/additonal_script_css/additional_styles';

    private const XML_PATH_CATEGORY_URL_KEY_MAPPING = 'geo_page/category_page/url_key_mapping';

    private const XML_PATH_CATEGORY_NAME_MAPPING = 'geo_page/category_page/name_mapping';

    private const XML_PATH_WEBSITE_NAME = 'general/store_information/name';

    private $defaultValues = [
        'send_gift_block_content' => 'A {category} Gift Basket can be delivered to a gift recipient in {city},{region} as quickly as 90 minutes. If you send a {website} {city} {category} Gift Basket same day hand delivery, the gift basket will be delivered by end of the day!',
        'free_shipping_block_content' => 'Looking for {category} gift baskets? Send a gift basket from {website}! We offer free delivery on all orders over $100 (per delivery location) anywhere in {country}, including {city}. Our gift basket delivery services to {city} are money-back guaranteed services. Order any of our {category} gift baskets delivered to {city}, {country}. Buy the best gift basket online now.',
        'same_day_delivery_block_content' => 'If you need a {category} gift basket delivered to {city}, we offer a same-day delivery service to {city}, {region}, {country}. The cut-off time for same-day delivery is 11 A.M. EST. And remember, you can order a {category} gift basket online, delivered to {city}, 24/7 & 365 days a year. Send a beautiful gift basket to your loved one today.',
        'next_day_delivery_block_content' => "Send a {category} gift basket to someone special from {website}! For gift baskets delivered throughout {city}, {region} and {country}, we offer multiple delivery options. At checkout, you will see all the different delivery methods plus an estimated number of days for your delivery to be made. With our wide selection of gift baskets, including gourmet treats, wine and beer, and more, you're sure to find something that your recipient will love.",
        'any_day_delivery_block_title' => "Fast & Efficient Gift Basket Delivery to {city}",
        'any_day_delivery_block_content' => "Send a {category} gift basket to someone special from {website}! For gift baskets delivered throughout {city}, {region} and {country}, we offer multiple delivery options. At checkout, you will see all the different delivery methods plus an estimated number of days for your delivery to be made. With our wide selection of gift baskets, including gourmet treats, wine and beer, and more, you're sure to find something that your recipient will love.",
        'meta_title' => '{city} {category} Gift Basket Delivery | Send {category} Gift Basket Delivery to {city}, {region} | {website}',
        'meta_description' => 'Buy {category} Gift Baskets Online - Free Shipping to {city} ({region}, {country}) | {delivery} Delivery in {city} | Over 3200 Custom Designs ⏩ Order Now!',
        'meta_robot' => 'INDEX,FOLLOW',
        'faq' => '',
        'slider' => '<div data-content-type="block" data-appearance="default" data-element="main">{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="%s" type_name="CMS Static Block"}}</div>',
        'popular_product_limit' => self::CITY_PAGE_DEFAULT_POPULAR_PRODUCT_LIMIT,
        'review_product_limit' => self::PRODUCT_REVIEW_LIST_DEFAULT_LIMIT,
        'category_city_page_limit' => self::CITY_PAGE_DEFAULT_CATEGORY_PAGE_LIST_LIMIT,
        'related_city_limit' => self::RELATED_CITY_LIST_DEFAULT_LIMIT,
        'block2_content_title' => 'Send a Delicious Gift Basket from {website}!',
        'block2_content' => 'Show your love with a {city} gift basket from {website}! Our {category} gift baskets feature savory snacks, wines, cakes, cupcakes, chocolates, champagnes, and much more. We deliver to {city}, {region}, {country}. Shop online now and order the best gift basket on the market for your friend or loved one.',
        'block3_title' => 'Explore Our Collections for Every Occasion!',
        'three_col_block_col1' => "Discover a selection of {city} gift baskets from {website}! You can easily get your order with our same-day, next-day, or other fast delivery options to {city}, {region}, {country}. Your carefully curated {category} gift basket will be delivered right to your doorstep.",
        'three_col_block_col2' => "For a gift that's guaranteed to bring a smile, try a {city} gift basket from {website}! With our same-day, next-day, or other fast delivery options to {city}, {region}, {country}, you can send a gift basket now!",
        'three_col_block_col3' => "Send your heartfelt wishes with a {category} gift basket from {website}! We offer different delivery options to {city}, {region}, {country}. Pick the one best suited to your needs.",
        'freeshipping_block_title' => 'Free Shipping to %1, %2',
        'next_day_delivery_block_title' => 'Fast & Efficient Gift Basket Delivery to %1',
        'faq_ans_1' => 'Looking to send a gift basket to someone in %1? Below are the most popular gift baskets in %1.',
        'faq_ans_2' => 'It can vary depending on several factors, including the shipping method selected and the destination within %1. Shipping options like same-day, next-day, 1-day, 2-day or 3-day delivery are also available for fast delivery.',
        'faq_ans_3' => "Consider the recipient's interests, preferences, and the occasion. For example, if they love wine, a wine and cheese gift basket may be ideal. If it's a corporate gift, choose something appropriate for a professional setting. Factor in your relationship with the person and your budget, and think about the size, presentation, and personal touches that would make the gift special.",
        'faq_qn_1' => 'What are the most popular gift baskets in %1?',
        'faq_qn_2' => 'How long does it take to get a %1basket delivered to %2, %3, %4?',
        'faq_qn_3' => 'How do I choose the right gift basket for someone?',
        'three_col_heading_1' => 'Delightful Gift baskets Delivered <br/>Right to Your Doorstep!',
        'three_col_heading_2' => 'Send the Perfect Gift Basket: <br/>Delivered with Love!',
        'three_col_heading_3' => 'Surprise & Delight with a Gift Basket: <br/>Get Yours Delivered Today!',
        'city_page_title' => '%1 Gift Basket Delivery',
        'category_page_title' => '%1 Gift Basket Delivery In %2',
        'state_page_title' => 'Gift Basket Delivery In %1',
        'same_day_block_title' => 'Same-Day Gift Delivery to %1',
        'same_day_category_block_title' => 'Same-Day %1 Gift Delivery to %2',
        'state_page' => [
            'faq_qn_2' => 'How long does it take to get a basket delivered to %1, %2?'
        ]
    ];

    private $storeSpecificDefaultValues = [
        'yuview' => [
            'freeshipping_block_title' => 'Enjoy Free Shipping to %1, %2!',
            'next_day_delivery_block_title' => 'Prompt & Problem-Free Gift Basket Delivery to %1',
            'next_day_delivery_block_content' => 'Please a friend or loved one with a {category} gift basket from {website}! We deliver to {city}, {region} and {country}. At checkout, you will see all the different delivery methods we offer plus an estimated number of days for the gift basket to be delivered. Explore our collections according to the occasion or holiday you’re celebrating and discover the perfect gift today!',
            'faq_ans_1' => "Check out the most gift baskets below, if you're looking to send a gift basket in %1. Take a look at the most popular gift baskets below, if you're looking to send a gift basket in %1. Below are some of the most popular gift baskets in %1, if you're looking to send a gift basket to someone special in %1.",
            'faq_ans_2' => "The delivery time for a gift basket to %1, %2, %3 can vary depending on the chosen shipping method and the destination within %1. We offer fast delivery options for your convenience, including same-day, next-day, 1-day, 2-day, or 3-day delivery.",
            'faq_ans_3' => "To find the perfect gift basket, start by considering the recipient's interests and preferences. A wine and cheese gift basket, for instance, would be the perfect option if they have a love for wine. Consider the occasion for the gift as well, such as a birthday, anniversary, or special day, as this can influence your selection. And always keep in mind that customizing the basket to match their preferences will make the perfect gift basket.",
            'any_day_delivery_block_title' => "Prompt & Problem-Free Gift Basket Delivery to %1",
            'any_day_delivery_block_content' => "Please a friend or loved one with a {category} gift basket from {website}! We deliver to {city}, {region} and {country}. At checkout, you will see all the different delivery methods we offer plus an estimated number of days for the gift basket to be delivered. Explore our collections according to the occasion or holiday you’re celebrating and discover the perfect gift today!",
            'free_shipping_block_content' => 'If you need a {category} gift basket, browse our website to find your perfect fit. Your recipient is sure to appreciate a gift basket from {website}. To sweeten the deal even more, we provide free delivery on all orders over $100 (per delivery location) in {country}, including {city}. In addition, our gift basket delivery services to {city} are money-back guaranteed services. Get any of our {category} gift baskets delivered to {city}, {country} with us! Order the best of the best gift baskets now at {website}.',
            'same_day_delivery_block_content' => "Are you looking for unique {category} gift baskets in {city}? Send {category} gift baskets with {website} and get them delivered right to your recipient's doorstep right away with our same-day delivery service to {city}, {region}, {country}. The cut-off time for same-day delivery is 11 A.M. EST. Have your gift delivered today.",
            'faq_qn_1' => 'What gift baskets are most popular in %1?',
            'faq_qn_2' => 'How long does it take to deliver a %1gift basket to %2, %3, %4?',
            'faq_qn_3' => 'How do I find the perfect gift basket?',
            'block2_content_title' => 'Delight a Friend or Loved One with a Gift Basket from {website}!',
            'block2_content' => 'Send your friend or loved one a splendid {city} gift basket from {website}! Featuring gourmet snacks, wines, cakes, chocolates, and other items, our {category} gift baskets are sure to please. We’re proud to offer delivery to {city}, {region}, {country}. Explore our collection online. You are sure to find something you like! Buy online now and enjoy gift-giving to the fullest.',
            'block3_title' => 'We Offer Gift Baskets for Every Occasion & Holiday!',
            'three_col_block_col1' => 'Delight your loved ones with a {city} gift basket from {website}! Have your gifts delivered right away with our same-day, next-day, or other fast delivery options to {city}, {region}, {country}. Select from our {category} gift basket collection.',
            'three_col_block_col2' => 'Try a {category} gift basket from {website}! You can send a gift basket that will surely brighten your recipient’s day with our same-day, next-day and other fast delivery options to {city}, {region}, {country}.',
            'three_col_block_col3' => "Consider sending a {city} gift basket from {website}! With our fast delivery options, including same-day, next-day, you can send a delightful gift basket that's sure to bring smiles to {city}, {region}, {country}.",
            'meta_title' => "{category} Gift Basket {city} | Send Gift Baskets Delivery To {city}, {region} | {website}",
            'meta_description' => "{category} Gift Baskets Online - {delivery} Delivery in {city}, {region} | Free Delivery in the {country} ⏩ Shop Now!",
            'state_page' => [
                'faq_qn_2' => 'How long does it take to deliver a gift basket to %1, %2?',
                'faq_ans_2' => "The delivery time for a gift basket to %1, %2 can vary depending on the chosen shipping method and the destination within %1. We offer fast delivery options for your convenience, including same-day, next-day, 1-day, 2-day, or 3-day delivery.",
            ]
        ],
        'northviewca' => [
            'freeshipping_block_title' => 'We Offer Free Shipping to %1, %2!',
            'next_day_delivery_block_title' => 'Fast Gift Basket Delivery to %1',
            'next_day_delivery_block_content' => 'Show your love with a {category} gift basket from {website}! We deliver to {city}, {region} and {country}. At checkout, you will be able to choose your preferred delivery method. You will also see an estimated number of days for the delivery to be made. Send the perfect Christmas gift basket today!',
            'faq_ans_1' => "Before you send a %1 gift basket, check out the most popular gift baskets in %1 below.",
            'faq_ans_2' => "The shipping time depends on the shipping method selected and the destination within %1. Our delivery options include same-day, next-day, 1-day, 2-day, and 3-day delivery.",
            'faq_ans_3' => "This will depend on how well you know your friend or loved one! For those who love to celebrate, a champagne gift basket might be perfect! Those who love savoury snacks will enjoy a Christmas gourmet gift basket while health-minded people would enjoy a healthy Christmas gift basket. You can also customize or upgrade any of our gift baskets with your recipient in mind.",
            'any_day_delivery_block_title' => "Fast Gift Basket Delivery to %1",
            'any_day_delivery_block_content' => "Show your love with a {category} gift basket from {website}! We deliver to {city}, {region} and {country}. At checkout, you will be able to choose your preferred delivery method. You will also see an estimated number of days for the delivery to be made. Send the perfect Christmas gift basket today!",
            'free_shipping_block_content' => 'Looking for a {category} gift basket this holiday season? You are sure to find the perfect fit at {website}! We offer free delivery on all orders over $100 (per delivery location) in {country}, including {city}. There’s more! Our gift basket delivery services to {city} are money-back guaranteed services. Enjoy having any of our {category} gift baskets delivered to {city}, {country} by our elves at {website}! Order an amazing holiday gift basket at {website} now.',
            'same_day_delivery_block_content' => "Are you looking for unique {category} gift baskets in {city}? Send {category} gift baskets with {website} and get them delivered right to your recipient's doorstep right away with our same-day delivery service to {city}, {region}, {country}. The cut-off time for same-day delivery is 11 A.M. EST. Have your gift delivered today.",
            'faq_qn_2' => 'How long does it take to deliver a %1gift basket to %2, %3, %4?',
            'faq_qn_3' => 'How do I choose the perfect gift basket for someone?',
            'block2_content_title' => 'This Holiday Season, Send a Gift Basket from {website}!',
            'block2_content' => '‘Tis the season of gift-giving, so share the perfect {city} gift basket from {website} with a friend or loved one! Our gift baskets feature delicious treats, cakes, chocolates, champagnes, baked goods, and other items in the spirit of the season. We offer delivery to {city}, {region}, {country}. Buy online now and discover the joy of giving!',
            'block3_title' => 'Choose The Perfect Gift Basket!',
            'three_col_block_col1' => 'Surprise and please your loved ones with a {city} gift basket from {website}! We offer same-day, next-day, and other fast delivery methods to {city}, {region}, {country}. Select any {category} gift basket that tickles your fancy.',
            'three_col_block_col2' => 'Send a {category} gift basket from {website}! Take advantage of our same-day, next-day, and other fast delivery options to {city}, {region}, {country}. Shop online now.',
            'three_col_block_col3' => "This holiday season, send a {city} gift basket from {website}! Our fast delivery options include same-day and next-day delivery, so send a gift basket to {city}, {region}, {country}.",
            'meta_title' => "Christmas {category} Gift Baskets Delivered to {city}, {region} | {website}",
            'meta_description' => "Get Christmas {category} Gift Baskets & Boxes Delivered in Time For the Holiday - Free Shipping to {city}, {region}({country}) ⏩ Buy now",
            'three_col_heading_1' => 'Holiday Gift Baskets Delivered to Your Recipient!',
            'three_col_heading_2' => 'Get Your Gift Basket Delivered with Love and Affection!',
            'three_col_heading_3' => 'Show Your Love with the Perfect Gift Basket!',
            'city_page_title' => 'Christmas Gift Basket Delivery In %1',
            'category_page_title' => 'Christmas %1 Gift Basket Delivery In %2',
            'state_page_title' => 'Christmas Gift Basket Delivery In %1',
            'state_page' => [
                'faq_qn_2' => 'How long does it take to deliver a gift basket to %1, %2?'
            ]
        ],
        'northpoleview' => [
            'freeshipping_block_title' => 'Take Advantage of Free Shipping to %1, %2!',
            'next_day_delivery_block_title' => 'We Offer Prompt & Efficient Gift Basket Delivery to %1',
            'next_day_delivery_block_content' => 'Discover our collection of {category} gift baskets at {website}! Have any of our gift baskets delivered to {city}, {region} and {country} by our hardworking and efficient elves. At checkout, simply select your delivery method. You will see an estimated number of days that the delivery will require. Show your love and generosity with an amazing Christmas gift basket today!',
            'faq_ans_1' => "Looking to send a %1 gift basket? Discover the most popular gift baskets in %1 in our list below.",
            'faq_ans_2' => "The answer will depend on the delivery method selected. Our fast delivery options include same-day, next-day, 1-day, 2-day, and 3-day delivery.",
            'faq_ans_3' => "For those who love champagne, a champagne gift basket is the perfect choice. For lovers of savory snacks or chocolate, a gourmet gift basket or a chocolate gift would be perfect. You can also customize or upgrade any of our gift baskets to make sure they fit your recipient’s unique wants and needs.",
            'any_day_delivery_block_title' => "We Offer Prompt & Efficient Gift Basket Delivery to %1",
            'any_day_delivery_block_content' => "Discover our collection of {category} gift baskets at {website}! Have any of our gift baskets delivered to {city}, {region} and {country} by our hardworking and efficient elves. At checkout, simply select your delivery method. You will see an estimated number of days that the delivery will require. Show your love and generosity with an amazing Christmas gift basket today!",
            'free_shipping_block_content' => 'If you need a {category} gift basket this holiday season, we have just what you need! Here at {website}, we’re proud to offer free delivery on all orders over $100 (per delivery location) in {country}, including {city}. Moreover, our gift basket delivery services to {city} are money-back guaranteed services. Have any of our {category} gift baskets delivered to {city}, {country} by our hardworking and dedicated elves! Order online now.',
            'same_day_delivery_block_content' => "Are you looking for unique {category} gift baskets in {city}? Send {category} gift baskets with {website} and get them delivered right to your recipient's doorstep right away with our same-day delivery service to {city}, {region}, {country}. The cut-off time for same-day delivery is 11 A.M. EST. Have your gift delivered today.",
            'faq_qn_1' => 'Which gift baskets are the most popular in %1?',
            'faq_qn_2' => 'How long does a delivery to %2, %3, %4?',
            'faq_qn_3' => 'How do I select the perfect gift basket for a friend or loved one?',
            'block2_content_title' => 'Send a Christmas Gift Basket from {website}!',
            'block2_content' => 'This Christmas season, show your generosity of spirit with a {city} gift basket from {website}! From champagnes to cakes, chocolates to baked goods, and everything in between, our gift baskets are sure to rock the holidays. Enjoy fast and efficient delivery to {city}, {region}, {country} with {website}. Order online now and show your love!',
            'block3_title' => 'Select Any of Our Gift Baskets for the Perfect Celebration!',
            'three_col_block_col1' => 'This holiday season, show your love a {city} gift basket from {website}! We offer same-day, next-day, and other fast delivery options to {city}, {region}, {country}. Choose any of our {category} gift baskets now.',
            'three_col_block_col2' => 'Put your love on display with a {category} gift basket from {website}! Choose your delivery option to {city}, {region}, {country}. Shop online now.',
            'three_col_block_col3' => "This Christmas season, delight a friend or love one with a {city} gift basket from {website}! We offer same-day and next-day delivery options. Send a gift basket to {city}, {region}, {country} now.",
            'meta_title' => "{city} Christmas {category} Gift Baskets | Free Delivery in {city}, {region} | {website}",
            'meta_description' => "Sending Christmas {category} Gift Baskets to Friends & Family Spreads - Free Shipping to {city} ({region}, {website}) ⏩ Shop today",
            'three_col_heading_1' => 'Have a Christmas Gift Basket Delivered with Us!',
            'three_col_heading_2' => 'Enjoy Fast Delivery This Holiday Season!',
            'three_col_heading_3' => 'Send the Perfect Christmas Gift Basket with Us!',
            'city_page_title' => 'Christmas Gift Basket Delivery In %1',
            'category_page_title' => 'Christmas %1 Gift Basket Delivery In %2',
            'state_page_title' => 'Christmas Gift Basket Delivery In %1',
            'state_page' => [
                'faq_qn_2' => 'How long does a delivery to %1, %2?'
            ]
        ],
        'muttsviewca' => [
            'freeshipping_block_title' => 'Take Advantage of Free Shipping to %1, %2 with Mutts & Mousers!',
            'next_day_delivery_block_title' => 'Efficient Gift Delivery to %1',
            'next_day_delivery_block_content' => 'Show your love with a gift for pets and pet owners! Here at {website}, we deliver gifts to {city}, {region} and {country}. At checkout, you will be able to select the desired delivery method. You will also see an estimated number of days for the delivery. Send the perfect gift for a pet or pet owner now!',
            'faq_ans_1' => "Check out the most popular gifts for pets and pet owners in %1 below.",
            'faq_ans_2' => "That will depend on your delivery option of choice. Our delivery methods include same-day, next-day, 1-day, 2-day, and 3-day delivery.",
            'faq_ans_3' => "We offer a variety of wonderful gifts for cats and dogs as well as their owners. You can also customize or upgrade your gift with add-on gourmet foods, wines, stuffed animals, and other items to make it suit your unique recipient.",
            'any_day_delivery_block_title' => "Efficient Gift Delivery to %1",
            'any_day_delivery_block_content' => "Show your love with a gift for pets and pet owners! Here at {website}, we deliver gifts to {city}, {region} and {country}. At checkout, you will be able to select the desired delivery method. You will also see an estimated number of days for the delivery. Send the perfect gift for a pet or pet owner now!",
            'free_shipping_block_content' => 'Send a pet or pet owner an amazing, delicious gift from {website}! Our gift boxes feature toys and treats for pets as well as gifts for their proud owners. We offer free delivery on all orders over $100 (per delivery location) in {country}, including {city}. In addition, our gift basket delivery services to {city} are money-back guaranteed services for your convenience. Have any of our gifts delivered to {city}, {country} by our dedicated professionals at {website}! Order online now.',
            'same_day_delivery_block_content' => "Need a gift delivered on the same day? If you order your gift by 11 A.M. EST, you can have it delivered same day with {website}! Delight a pet or pet owner with a gift from {website}. Buy online now.",
            'faq_qn_1' => 'What are the most popular gifts for pets and pet owners in %1?',
            'faq_qn_2' => 'How long does it take to deliver a gift to %2, %3, %4?',
            'faq_qn_3' => 'How do I choose the best gift for a pet or pet owner?',
            'block2_content_title' => 'Send a Pet Owner a Gift from {website}!',
            'block2_content' => 'Show your love for a pet or pet owner with the perfect {city} gift from {website}! We offer gift boxes for cats and dogs as well as their proud owners. Enjoy gift delivery to {city}, {region}, {country} and see your recipient’s eyes light up with joy. Explore our collections to find your perfect match. Order online now.',
            'block3_title' => 'Discover Our Gifts for Pets & Pet Owners!',
            'three_col_block_col1' => 'Delight a pet or pet owner with a {city} gift basket from {website}! We offer same-day, next-day, and other fast delivery methods to {city}, {region}, {country}. Discover your favourite gift now.',
            'three_col_block_col2' => 'Send the perfect gift for a pet or pet owner from {website}! We offer same-day, next-day, and other fast delivery options to {city}, {region}, {country}. Order now.',
            'three_col_block_col3' => "Send the perfect {city} gift for a pet or pet owner from {website}! Our fast delivery options include same-day and next-day delivery. Send a gift to {city}, {region}, {country} with us. Buy online now.",
            'meta_title' => "{city} Gifts For Dogs, Cats and Their Owners | Free Delivery in {city}, {region} | {website}",
            'meta_description' => "Prepare For Wagging Tails and Loud Purrs With Gift Delivery to {city}, {region} | Free Delivery in the {country} ⏩ Buy Now!",
            'three_col_heading_1' => 'Pet & Pet Owner Gifts Delivered Fast!',
            'three_col_heading_2' => 'We Deliver Gifts for Pets & Pet Owners!',
            'three_col_heading_3' => 'Show You Care with a Gift from Mutts & Mousers!',
            'city_page_title' => 'Gift Basket For Pets & Pet Owners In %1',
            'state_page_title' => 'Gift Basket For Pets & Pet Owners In %1',
            'state_page' => [
                'faq_qn_2' => 'How long does it take to deliver a gift to %1, %2?'
            ]
        ],
        'laview' => [
            'freeshipping_block_title' => 'Enjoy Free Shipping to %1, {region}, %2',
            'next_day_delivery_block_title' => 'Fast Gift Basket Delivery to %1',
            'next_day_delivery_block_content' => 'Please a friend or loved one with a {category} gift basket from {website}! We deliver to {city}, {region}, {country}. At checkout, you will see all the different delivery methods plus an estimated number of days for the gift basket to be delivered. Explore our collection now.',
            'faq_ans_1' => "Check out the most popular gift baskets in %1 below.",
            'faq_ans_2' => "Fast delivery options like same-day, next-day, 1-day, 2-day, or 3-day delivery are available for your convenience.",
            'faq_ans_3' => "We offer gift baskets for every occasion and holiday. For those who love gourmet foods, a gourmet gift basket would be ideal. Wine lovers would appreciate wine gift baskets. You can customize any of our gift baskets to make sure they suit your recipient’s unique needs.",
            'any_day_delivery_block_title' => "Fast Gift Basket Delivery to %1",
            'any_day_delivery_block_content' => "Please a friend or loved one with a {category} gift basket from {website}! We deliver to {city}, {region}, {country}. At checkout, you will see all the different delivery methods plus an estimated number of days for the gift basket to be delivered. Explore our collection now.",
            'free_shipping_block_content' => 'Our collections feature beautifully crafted gift baskets for friends and loved ones. We offer free delivery on all orders over $100 (per delivery location) in {country}, including {city}. Our gift basket delivery services to {city} are money-back guaranteed services. The professionals at {website} will deliver any gift basket you choose to {city}, {region}, {country} in a fast and efficient manner. Buy online now.',
            'same_day_delivery_block_content' => "If you need a gift basket delivered to {city}, take advantage of our same-day delivery service to {city}, {region}, {country}. The cut-off time for same-day delivery is 11 A.M. EST. And remember, you can order a gift basket online, delivered to {city}, 24/7, 365 days a year. Send a beautiful gift basket to your loved one today.",
            'faq_qn_1' => 'What are the most popular gift baskets in %1?',
            'faq_qn_2' => 'How long does it take to deliver a gift basket to %2, %3, %4?',
            'faq_qn_3' => 'How do I choose the perfect gift basket?',
            'block2_content_title' => 'Discover Gift Baskets at {website}',
            'block2_content' => 'Send your lucky recipient a spectacular gift basket from {website}! Featuring gourmet foods, rich wines, cakes, and other items, our gift baskets are perfect for a wide variety of occasions. We offer delivery to {city}, {region}, {country}. Buy online now.',
            'block3_title' => 'Fast & Efficient Gift Basket Delivery',
            'three_col_block_col1' => 'Show your love with a {city} gift basket from {website}! Have your gifts delivered right away with our same-day, next-day, or other fast delivery options to {city}, {region}, {website}. Choose your perfect gift basket from our collection.',
            'three_col_block_col2' => 'Enjoy a gift basket from {website}! You can send a gift basket with our same-day, next-day, and other fast delivery options to {city}, {region}, {country}.',
            'three_col_block_col3' => "Send a {city} gift basket from {website}! Our fast delivery options include same-day and next-day options. You can send a delightful gift basket to {city}, {region}, {country}.",
            'three_col_heading_1' => 'We Offer Gift Baskets Delivered to {city}!',
            'three_col_heading_2' => 'Discover Amazing Gift Baskets at {website}!',
            'three_col_heading_3' => 'Show Your Love with a Gift Basket!',
            'meta_title' => "{city} {delivery} {category} Gift Basket Delivery",
            'meta_description' => "{website} is {region}'s #1 Gift Basket Company! - Free {delivery} {category} Gift Basket Delivery in {city} | We offer 4,000 Custom Designs ⏩ Order Now!",
            'state_page' => [
                'faq_qn_2' => 'How long does it take to deliver a gift basket to %1, %2?'
            ]
        ],
        'newjerseyview' => [
            'freeshipping_block_title' => 'Enjoy free shipping to %1, %2 with {website}!',
            'next_day_delivery_block_title' => 'Efficient and Reliable Gift Basket Delivery to %1',
            'next_day_delivery_block_content' => 'Bring some happiness with a luxurious gift basket from New Jersey Gift Baskets! We offer different delivery options across {city}, {region}, {country}. When you check out, you will see all the delivery methods available and an estimated number of days for your order to be delivered. Our gift baskets are filled with delicious wines, beers, cakes, cookies and more that will please every palate and make for a memorable gift.',
            'faq_ans_1' => "The following are the most popular gift baskets in %1, {region}, {country}.",
            'faq_ans_2' => "The delivery time for your gift basket vary according to the delivery option you choose. Our gift baskets are delivered in a variety of ways. We offer same-day delivery, next-day delivery, 1-day delivery, 2-day delivery, and 3-day delivery service.",
            'faq_ans_3' => "You can choose from a variety of gift baskets on our website. Our selection includes wines, beers, gourmet snacks, and more that will fit any occasion. You can even personalize any of our baskets based on your preferences. Your recipient will surely appreciate the thoughtfulness behind your choice.",
            'any_day_delivery_block_title' => "Efficient and Reliable Gift Basket Delivery to %1",
            'any_day_delivery_block_content' => "Bring some happiness with a luxurious gift basket from New Jersey Gift Baskets! We offer different delivery options across {city}, {region}, {country}. When you check out, you will see all the delivery methods available and an estimated number of days for your order to be delivered. Our gift baskets are filled with delicious wines, beers, cakes, cookies and more that will please every palate and make for a memorable gift.",
            'free_shipping_block_content' => "Give someone you love a gift they'll never forget with {website}! We deliver to your doorstep anywhere in the {country}, including {city}, for free on all orders over $100. We assure you that our gift basket delivery services to {city} are 100% money-back guaranteed. Purchase any of our delightful gift baskets delivered to {city}, {region}, {country}. Shop online today.",
            'same_day_delivery_block_content' => "If you need a gift basket delivered to {city}, take advantage of our same-day delivery service to {city}, {region}, {country}. The cut-off time for same-day delivery is 11 A.M. EST. And remember, you can order a gift basket online, delivered to {city}, 24/7, 365 days a year. Send a beautiful gift basket to your loved one today.",
            'faq_qn_1' => 'What are the most popular gift baskets in %1, {region}, {country}?',
            'faq_qn_2' => 'How long does it take to get a basket delivered to %2, %3, %4?',
            'faq_qn_3' => 'How do I choose the perfect gift basket for someone in {city}, {region}, {country}?',
            'block2_content_title' => 'Give a Delectable Gift Basket from {website}!',
            'block2_content' => 'Send your lucky recipient a spectacular gift basket from {website}! Featuring gourmet foods, rich wines, cakes, and other items, our gift baskets are perfect for a wide variety of occasions. We offer delivery to {city}, {region}, {country}. Buy online now.',
            'block3_title' => 'Check Out Our Extensive Collection for Every Occasion & Holiday Season!',
            'three_col_block_col1' => 'Let us help you send the perfect gift basket to your special someone! Have your gift delivered quickly with our same-day, next-day, or other delivery options to {city}, {region}, {country}. Explore our website now.',
            'three_col_block_col2' => "For a gift that's sure to be a hit, check out the beautiful gift baskets at {website}! We offer a variety of delivery options, including same-day and next-day delivery service to {city}, {region}, {country}. Buy online now.",
            'three_col_block_col3' => "{website} will deliver your warmest wishes with a delightful gift basket! We offer several delivery options to {city}, {region}, {country}, including same-day and next-day delivery. Shop now for great deals.",
            'three_col_heading_1' => 'Send a Gift Basket with {website}!',
            'three_col_heading_2' => 'Direct Gift Basket Delivery to Your Recipient!',
            'three_col_heading_3' => 'We Deliver Wonderful Gift Baskets for Your Loved Ones!',
            'meta_title' => "{delivery} {category} Gift Basket Delivery in {city}",
            'meta_description' => "Free {delivery} {category} Gift Baskets Delivery in {city} - America's #1 Company Offering 4,000 Different Gift Baskets for Every Occasion ⏩ Buy Now!",
            'city_page_title' => '%1 Gift Basket Delivery',
            'category_page_title' => '%1 %2 Gift Basket Delivery',
            'state_page_title' => '%1 Gift Basket Delivery',
            'state_page' => [
                'faq_qn_1' => 'What are the most popular gift baskets in %1, {country}?',
                'faq_qn_2' => 'How long does it take to get a basket delivered to %1, %2?',
                'faq_qn_3' => 'How do I choose the perfect gift basket for someone in {region}, {country}?'
            ]
        ],
        'nycbview' => [
            'freeshipping_block_title' => 'We Offer Free Shipping to %1, %2',
            'next_day_delivery_block_title' => 'Hassle-Free Gift Basket Delivery to %1',
            'next_day_delivery_block_content' => 'Spread a little sunshine to someone’s day with an exquisite gift basket from {website}! We offer a variety of delivery methods to all parts of {city}, {region}, {country}. You will see all the available shipping options and estimated delivery dates when you proceed to check out. Discover a wide array of gift baskets filled with delicious wines, beers, cakes, cookies and more. Place your order on our website now.',
            'faq_ans_1' => "Here are the most popular gift baskets in {city}, {region}, {country}.",
            'faq_ans_2' => "The delivery time for your gift basket to arrive will depend on the delivery option you choose. We offer same-day, next-day, 1-day, 2-day, and 3-day delivery service.",
            'faq_ans_3' => "We have a wide range of gift baskets for any occasion that are sure to delight your loved ones. Our wide selection of gift baskets includes wines, beers, gourmet snacks, and accessories that can be personalized to suit your needs.",
            'any_day_delivery_block_title' => "Hassle-Free Gift Basket Delivery to %1",
            'any_day_delivery_block_content' => "Spread a little sunshine to someone’s day with an exquisite gift basket from {website}! We offer a variety of delivery methods to all parts of {city}, {region}, {country}. You will see all the available shipping options and estimated delivery dates when you proceed to check out. Discover a wide array of gift baskets filled with delicious wines, beers, cakes, cookies and more. Place your order on our website now.",
            'free_shipping_block_content' => "Discover artfully crafted gift baskets from {website}. We offer free delivery on all orders over $100 (per delivery location) in the {country}, including {city}. Our gift basket delivery services to {city} are money-back guaranteed services. Our professionals at {website} will deliver your gift basket to {city}, the {country} in a hassle-free manner. Order online now.",
            'same_day_delivery_block_content' => "If you need a gift basket delivered to {city}, take advantage of our same-day delivery service to {city}, {region}, {country}. The cut-off time for same-day delivery is 11 A.M. EST. And remember, you can order a gift basket online, delivered to {city}, 24/7, 365 days a year. Send a beautiful gift basket to your loved one today.",
            'faq_qn_1' => 'What are the most popular gift baskets in %1, {region}, {country}?',
            'faq_qn_2' => 'How long does it take to get a basket delivered to %2, %3, %4?',
            'faq_qn_3' => 'How do I choose the perfect gift basket for someone in {city}, {region}, {country}?',
            'block2_content_title' => '{website} Offers Amazing Gift Baskets!',
            'block2_content' => 'Show your gratitude and appreciation with a {city} gift basket from {website}! Our gift baskets feature gourmet foods, rich wines, cakes, chocolates, and other items. We offer delivery to {city}, {region}, {country}. Buy online now.',
            'block3_title' => 'Fast Gift Basket Delivery',
            'three_col_block_col1' => 'Looking for a perfect way to show your appreciation to someone? Look no further than delicious gift baskets from {website}! Send a gift to {city}, {region}, {country} with our same-day, next-day, and other convenient delivery options. Shop with us today.',
            'three_col_block_col2' => "Touch your loved ones’ lives with a special gift basket delivery from {website}! With our variety of delivery options, including same-day and next-day delivery service to {city}, {region}, {country}, you can rest assured knowing your gift will be delivered quickly and efficiently.",
            'three_col_block_col3' => "Surprise someone special with a delectable gift basket {website}! Order with us today and experience fast delivery service with our same-day, next-day, or other delivery methods to {city}, {region}, {country}.",
            'three_col_heading_1' => 'Enjoy Online Shopping with {website}!',
            'three_col_heading_2' => 'Get Your Gift Baskets Delivered Right Now!',
            'three_col_heading_3' => 'Celebrate Any Occasion with {website}!',
            'meta_title' => "Get {delivery} Gift Basket Delivery in {city} | {website}",
            'meta_description' => "{website} is the #1 Gift Basket Co. delivering to {city}! - All of Our Gift Baskets Can Be Customized by Adding Wine, Champagne, Beer, Alcohol or Gourmet Foods. ⏩ Shop Now!",
            'city_page_title' => '%1 Gift Basket Delivery',
            'category_page_title' => '%1 %2 Gift Basket Delivery',
            'state_page_title' => '%1 Gift Basket Delivery',
            'state_page' => [
                'faq_qn_1' => 'What are the most popular gift baskets in %1, {country}?',
                'faq_qn_2' => 'How long does it take to get a basket delivered to %1, %2?',
                'faq_qn_3' => 'How do I choose the perfect gift basket for someone in {region}, {country}?'
            ]
        ],
        'torontobasketsview' => [
            'block2_content_title' => 'Surprise Someone with a Gift Basket from {website}!',
            'block2_content' => "Make them feel happy with a {category} gift basket surprise from {website}! Our gift baskets are packed with delicious treats like wines, champagne, chocolates, gourmet snacks, and more that's sure to bring joy. We deliver across {city}, {region}, {country}. Shop with us today!",
            'block3_title' => 'Choose Any of Our Gift Baskets for the Perfect Surprise!',
            'freeshipping_block_title' => 'Free Shipping Available to %1, %2!',
            'free_shipping_block_content' => "If you’re looking for an ideal gift for an upcoming occasion, look no further than a {category} gift basket from {website}! For all orders over $100 (per delivery location) in {city}, {region}, {country}, we proudly offer free delivery services for all of our beloved customers. Furthermore, our gift basket delivery services to {city} are money-back guaranteed services. Have any of our beautiful gift baskets delivered right away to {city}, {country}. Buy online today to find the perfect gift.",
            'next_day_delivery_block_title' => 'Reliable & Efficient Gift Basket Delivery to %1',
            'next_day_delivery_block_content' => 'At {website}, we have a wide selection of {category} gift baskets! Have your gift basket promptly delivered to {city}, {region}, {country}. You can choose your delivery method upon checkout, and estimated delivery days will be displayed. Browse our website to discover our collection today.',
            'any_day_delivery_block_title' => "Reliable & Efficient Gift Basket Delivery to %1",
            'any_day_delivery_block_content' => "At {website}, we have a wide selection of {category} gift baskets! Have your gift basket promptly delivered to {city}, {region}, {country}. You can choose your delivery method upon checkout, and estimated delivery days will be displayed. Browse our website to discover our collection today.",
            'same_day_delivery_block_content' => "Take advantage of our same-day delivery service to {city}, {region}, {country}. To ensure same-day delivery, place your order by 11 A.M. EST. You can order a gift basket online 24/7, 365 days a year. Choose the ideal gift basket now.",
            'faq_qn_1' => 'Which gift baskets are the most popular in %1?',
            'faq_qn_2' => 'How long does a delivery to %2, %3, %4 usually take?',
            'faq_qn_3' => 'How do I choose the perfect gift basket for someone special?',
            'faq_ans_1' => "If you're looking to send a {city} gift basket, see our list of popular gift baskets below.",
            'faq_ans_2' => "That will depend on the delivery method you choose. We offer same-day, next-day, 1-day, 2-day, and 3-day for fast delivery service.",
            'faq_ans_3' => "We have a variety of stunning gift baskets you can choose from. For those who love fruits, a fruit gift basket is the ideal option. For the wine lover in your life, a wine and cheese gift basket would be the perfect gift. You can personalize any of our gift baskets to create the perfect gift your special someone.",
            'three_col_heading_1' => 'Order a Delightful Gift Basket from {website}!',
            'three_col_heading_2' => 'Experience Hassle-Free Delivery Service with Us!',
            'three_col_heading_3' => 'Spread Happiness with the Perfect Gift Basket!',
            'three_col_block_col1' => 'Show your appreciation to your family member or significant other with a {city} gift basket from {website}! We offer multiple delivery options to {city}, {region}, {country} for your convenience, which include same-day and next-day delivery service. Browse our collection of {category} gift baskets now.',
            'three_col_block_col2' => "{website} has the perfect {category} gift basket for any occasion! Multiple delivery options are available, including same-day and next-day delivery service to {city}, {region}, {country}. Send the perfect gift today.",
            'three_col_block_col3' => "Explore an extensive collection of {category} gift baskets at {website}! Choose from our multiple delivery options to {city}, {region}, {country} today.",
            'meta_title' => "{category} Gift Baskets {city} Delivery",
            'meta_description' => "Offering 4,000 Different Gift Baskets for Every Occasion - Place your order for our {category} Gift Baskets in {city} today!  | Free Delivery in the {country} ⏩ Buy now",
            'city_page_title' => '%1 Gift Baskets',
            'category_page_title' => '%1 %2 Gift Baskets',
            'state_page_title' => '%1 Gift Baskets',
            'state_page' => [
                'faq_qn_2' => 'How long does a delivery to %1, %2 usually take?'
            ]
        ],
        'vanView' => [
            'block2_content_title' => 'Gifting Made Easy with {website}!',
            'block2_content' => "Wish someone well with a {category} gift basket from {website}! Our gift baskets are filled with delectable champagnes, beers, wines, and other special items that will delight your loved ones. We offer fast delivery service to {city}, British Columbia, {country}. Browse our website to choose the ideal gift basket for your loved ones today!",
            'block3_title' => 'Find the Perfect Gift Basket!',
            'freeshipping_block_title' => 'We Ship to Vancouver, %2 for Free!',
            'free_shipping_block_content' => "Looking for a thoughtful gift? {website} has some great ones! Free delivery services are available on all order over $100 (per delivery location) in {country}, including {city}. You'll also love our money-back guarantee on gift basket delivery services to {city}. We offer fast delivery of {category} gift baskets delivered to {city}, British Columbia, {country}. Order now and enjoy the convenience of online shopping with {website}.",
            'next_day_delivery_block_title' => 'Enjoy Fast Gift Basket Delivery to Vancouver',
            'next_day_delivery_block_content' => 'Discover our assortment of gift baskets at {website}! We deliver throughout {city}, British Columbia, {country}. At checkout, you will be able to choose your preferred delivery method and see an estimated number of days for delivery. Send a beautiful gift basket to your loved one today.',
            'any_day_delivery_block_title' => "Enjoy Fast Gift Basket Delivery to Vancouver",
            'any_day_delivery_block_content' => "Discover our assortment of gift baskets at {website}! We deliver throughout {city}, British Columbia, {country}. At checkout, you will be able to choose your preferred delivery method and see an estimated number of days for delivery. Send a beautiful gift basket to your loved one today.",
            'same_day_delivery_block_content' => "Need to send a gif basket to {city}? We offer same-day delivery service to {city}, British Columbia, {country}. Same-day delivery is available until 11 A.M. EST. You can send a gift basket to {city}, 24/7, 365 days a year. Explore our collection now.",
            'faq_qn_1' => 'What are the most popular gift baskets in %1?',
            'faq_qn_2' => 'How long does it take to deliver a gift basket to %2, British Columbia, %4?',
            'faq_qn_3' => 'How can I find the perfect gift basket for someone?',
            'faq_ans_1' => "If you’re looking to send a gift basket to {city}, take a look at the most popular gift baskets in {city} below.",
            'faq_ans_2' => "The estimated shipping time will vary depending on the shipping method selected and the destination within {city}. We offer same-day, next-day, 1-day, 2-day, and 3-day delivery options.",
            'faq_ans_3' => "If the person you're buying for has a love for wine and sweets, get them a wine and chocolate basket. If they like snacks, get them a gourmet food basket. {website} offers a variety of gift baskets in different styles, perfect for any occasion. Our gift baskets can be customized to suit the tastes of any recipient.",
            'three_col_heading_1' => 'We Deliver Gift Baskets Anywhere in Vancouver!',
            'three_col_heading_2' => 'Send Love with a Gift Basket Delivery!',
            'three_col_heading_3' => 'Say It with the Perfect Gift Basket from {website}!',
            'three_col_block_col1' => 'Extend your gratitude with a delicious {category} gift basket from {website} for a gift that they’ll never forget! Here at {website}, we offer same-day, next-day, and other fast delivery options to {city}, British Columbia, {country}. Select any {category} gift basket that best suits your needs.',
            'three_col_block_col2' => "Let someone know you love them with a {category} gift basket from {website}! Enjoy our same-day, next-day, and other fast delivery methods to {city}, British Columbia, {country}. Place your order today.",
            'three_col_block_col3' => "Put a smile on someone’s face with a {city} gift basket from {website}! We offer a variety of fast delivery options, including same-day and next-day delivery to {city}, British Columbia, {country}. Start shopping now.",
            'meta_title' => "{city} {category} Gift Baskets & Boxes Delivered",
            'meta_description' => "{website} is Canada's #1 Gift Basket Company Offering 4,000 Different Gift Baskets for Every Occasion | Free Delivery! ⏩ Order the perfect {category} Gift Basket now",
            'city_page_title' => 'Get Gift Basket Delivery in %1',
            'category_page_title' => 'Get %1 Gift Basket Delivery in %2',
            'state_page_title' => 'Get Gift Basket Delivery in %1',
            'state_page' => [
                'faq_qn_2' => 'How long does it take to deliver a gift basket to %1?'
            ]
        ],
        'hamiltonview' => [
            'block2_content_title' => '{website} Offers Delicious Gift Baskets!',
            'block2_content' => "Give a unique {city} gift basket from {website}! Featuring gourmet foods, rich wines, cakes, chocolates, and other items, our gift baskets are sure to please your recipient. We offer delivery to {city}, {region}, {country}. Order online now.",
            'block3_title' => 'Fast & Hassle-Free Gift Basket Delivery',
            'freeshipping_block_title' => 'Enjoy Free Shipping to %1, %2!',
            'free_shipping_block_content' => "Our collections feature delicious & beautifully crafted gift baskets. We offer free delivery on all orders over $100 (per delivery location) in {country}, including {city}. Our gift basket delivery services to {city} are 100% money-back guaranteed services. Our professionals at {website} will deliver your gift basket to {city}, {country} fast and hassle-free. Buy online now.",
            'next_day_delivery_block_title' => 'We Offer Hassle-Free Gift Basket Delivery to Hamilton',
            'next_day_delivery_block_content' => "Check out our extensive collection of {category} gift baskets at {website}! Order today and get your gift delivered fast with our same-day, next-day, and other delivery options to Hamilton, Ontario, Canada. When you check out, select your shipping method and you'll see an estimated number of days it will take for delivery. Shop online now.",
            'any_day_delivery_block_title' => "We Offer Hassle-Free Gift Basket Delivery to Hamilton",
            'any_day_delivery_block_content' => "Check out our extensive collection of {category} gift baskets at {website}! Order today and get your gift delivered fast with our same-day, next-day, and other delivery options to Hamilton, Ontario, Canada. When you check out, select your shipping method and you'll see an estimated number of days it will take for delivery. Shop online now.",
            'same_day_block_title' => 'Same-Day Gift Delivery to Hamilton',
            'same_day_category_block_title' => 'Same-Day Gift Delivery to Hamilton',
            'same_day_delivery_block_content' => "Warm the heart of someone special today with a {category} gift basket delivery from {website}! The cut-off time for same-day delivery is 11 A.M. EST. And remember, you can order a gift basket online, delivered to {city}, 24/7, 365 days a year. Order the best gift basket now.",
            'faq_qn_1' => 'Which gift baskets are the most popular in %1?',
            'faq_qn_2' => 'How long does a gift basket delivery to Hamilton, Ontario, Canada take?',
            'faq_qn_3' => 'How do I choose the perfect gift basket for a special someone? ',
            'faq_ans_1' => "Looking for a Hamilton gift basket? Find the most popular gift baskets in our list below.",
            'faq_ans_2' => "The delivery time depends on the delivery option you choose. For example, same-day delivery means that we can deliver your items today if ordered by 11 A.M. EST same day. Other delivery methods include next-day, 1-day, 2-day and 3-day delivery for your convenience.",
            'faq_ans_3' => "For the wine-and-chocolate lover in your life, get them a gourmet food basket. For someone who is vegan, get them a vegan gift basket overflowing with savoury and healthy goodies. We offer a variety of gift baskets that can be personalized to create a memorable gift.",
            'three_col_heading_1' => 'Fast Gift Basket Delivery Across {city}!',
            'three_col_heading_2' => 'Show Appreciation with {website}!',
            'three_col_heading_3' => 'Gift All Year Round with {website}!',
            'three_col_block_col1' => 'Send an exquisite {category} gift basket from {website}! With our same-day, next-day, and other fast delivery options to {city}, {region}, {country}, you can have your gift basket delivered at any time. Shop with us now.',
            'three_col_block_col2' => "Give someone you love a {city} gift basket! We offer a variety of hassle-free delivery options to {city}, {region}, {country}, including same-day and next-day delivery service. Order now.",
            'three_col_block_col3' => "Celebrate any occasion with a {category} gift basket from {website}! Our fast delivery options include same-day and next-day delivery to {city}, {region}, {country}. Shop now at great prices.",
            'meta_title' => "{city} {category} Gift Baskets, Boxes & Sets | Hamilton's #1",
            'meta_description' => "Shop {category} Gift Baskets Online - Free Shipping to {city} | 4,000 Different Gift Baskets for Every Occasion ⏩ Buy Now!",
            'city_page_title' => '%1 Gift Baskets, Boxes & Sets',
            'category_page_title' => '%1 %2 Gift Baskets, Boxes & Sets',
            'state_page_title' => '%1 Gift Baskets, Boxes & Sets'
        ],
        'otbasketview' => [
            'block2_content_title' => 'Shop Delicious Gift Baskets at {website}',
            'block2_content' => "Show you care with a {city} gift basket from {website}! Our gift baskets are perfect for any celebration. We’re proud to offer fast and efficient delivery to {city}, {region}, {country}. Explore our collections now.",
            'block3_title' => 'Fast Gift Basket Delivery',
            'freeshipping_block_title' => 'Take Advantage of Free Shipping to %1, %2',
            'free_shipping_block_content' => "Discover delicious gift baskets at {website}. We offer free delivery on all orders over $100 (per delivery location) in {country}, including {city}. Our gift basket delivery services to {city} are 100% money-back guaranteed services. Our professionals at {website} will deliver any gift basket you choose to {city}, {country}. Shop online now.",
            'next_day_delivery_block_title' => 'Fast & Reliable Gift Basket Delivery to %1',
            'next_day_delivery_block_content' => "Send someone you love a {city} gift basket from {website}! Our fast delivery options to {city}, {region}, {country} ensure that your gift basket arrives on time. At checkout, you will see all the different delivery methods we offer and how many days it will take for your gift basket to arrive. Buy online now.",
            'any_day_delivery_block_title' => "Fast & Reliable Gift Basket Delivery to %1",
            'any_day_delivery_block_content' => "Send someone you love a {city} gift basket from {website}! Our fast delivery options to {city}, {region}, {country} ensure that your gift basket arrives on time. At checkout, you will see all the different delivery methods we offer and how many days it will take for your gift basket to arrive. Buy online now.",
            'same_day_delivery_block_content' => "Take advantage of our same-day delivery service to {city}, {region}, {country}. The cut-off time for same-day delivery is 11 A.M. EST. You can order a gift basket online, delivered to {city}, 24/7, 365 days a year. Send a gift basket to your friend or loved one today.",
            'faq_qn_1' => 'What are the most popular {category} gift baskets in %1?',
            'faq_qn_2' => 'How long does it take to deliver a gift basket to %2, %3, %4?',
            'faq_qn_3' => 'How do I choose the right gift basket for a friend or a family member?',
            'faq_ans_1' => "Find the most popular {category} gift baskets in {city} below.",
            'faq_ans_2' => "We offer a variety of delivery options, including same-day, next-day, 1-day, 2-day, and 3-day delivery options.",
            'faq_ans_3' => "Our collections make it easy to choose the perfect present for your friends and loved ones. Our gift baskets feature delicious treats, rich wines, beers, and other items. You can customize your basket according to your tastes and preferences.",
            'three_col_heading_1' => 'We Deliver Gift Baskets Fast!',
            'three_col_heading_2' => 'Buy a Gift Basket Today!',
            'three_col_heading_3' => 'Show Your Love with a Gift Basket!',
            'three_col_block_col1' => 'Show your love with a {city} gift basket from {website}! We deliver fast and hassle-free to {city}, {region}, {country} with our same-day, next-day, and other fast delivery methods. Order online now.',
            'three_col_block_col2' => "At {website}, you can buy a {city} gift basket for yourself or someone you love! We offer multiple delivery options to {city}, {region}, {country}. Shop online now.",
            'three_col_block_col3' => "Deliver joy to your recipient with a {city} gift basket from {website}! We offer fast delivery options, including same-day and next-day delivery to {city}, {region}, {country}. Select your favourite gift basket and delight anyone on your list.",
            'meta_title' => "{city} {category} Gift Boxes & Gift Baskets Delivery",
            'meta_description' => "Order {category} Gift Baskets Online - FREE delivery to {city} | Over 4,000 Different Gift Boxes and Gift Baskets for Every Occasion ⏩ Shop Now!",
            'city_page_title' => '%1 Gift Baskets & Gift Boxes Delivery',
            'category_page_title' => '%1 %2 Gift Baskets & Gift Boxes Delivery',
            'state_page_title' => '%1 Gift Baskets & Gift Boxes Delivery',
            'state_page' => [
                'faq_qn_2' => 'How long does it take to deliver a gift basket to %1, %2?'
            ]
        ],
        'montview' => [
            'block2_content_title' => 'Discover Amazing Gift Baskets at {website}!',
            'block2_content' => "Send someone you love a {city} gift basket from {website}! Featuring gourmet foods, rich wines, chocolates, champagnes, cakes, and other items, our gift baskets are the perfect way to show your love. We offer delivery to {city}, {region}, {country}. Shop online now.",
            'block3_title' => 'We Offer Fast Gift Basket Delivery!',
            'freeshipping_block_title' => 'We Offer Free Shipping to %1, %2',
            'free_shipping_block_content' => "We offer expertly crafted gift baskets. Enjoy free delivery on all orders over $100 (per delivery location) in {country}, including {city}. Our gift basket delivery services to {city} are money-back guaranteed services. Our professionals at {website} will deliver any gift basket to {city}, {country}. Buy online now.",
            'next_day_delivery_block_title' => 'Fast & Hassle-Free Gift Basket Delivery to %1',
            'next_day_delivery_block_content' => "Send a {category} gift basket from {website} with our fast delivery options to {city}, {region}, {country}! At checkout, you will see all the different delivery methods we offer and how many days it will take for your gift basket to arrive. Explore our collection now.",
            'any_day_delivery_block_title' => "Fast & Hassle-Free Gift Basket Delivery to %1",
            'any_day_delivery_block_content' => "Send a {category} gift basket from {website} with our fast delivery options to {city}, {region}, {country}! At checkout, you will see all the different delivery methods we offer and how many days it will take for your gift basket to arrive. Explore our collection now.",
            'same_day_delivery_block_content' => "Need a gift basket delivered to {city}? Take advantage of our same-day delivery service to {city}, {region}, {country}. The cut-off time for same-day delivery is 11 A.M. EST. You can order a gift basket online, delivered to {city}, 24/7, 365 days a year. Send a wonderful gift basket to your loved one now.",
            'faq_qn_1' => 'What are the most popular gift baskets in %1?',
            'faq_qn_2' => 'How long does it take to deliver a gift basket to %2, %3, %4?',
            'faq_qn_3' => 'How do I choose the right gift basket for a friend or a loved one?',
            'faq_ans_1' => "Discover the most popular gift baskets in {city} below.",
            'faq_ans_2' => "We offer a variety of delivery options to suit your needs, including same-day, next-day, 1-day, 2-day, and 3-day delivery options.",
            'faq_ans_3' => "Our themed collections make it easy to choose the perfect gift basket for friends and loved ones. We offer an extensive selection of gift baskets featuring gourmet foods, fine wines, beers, and other items. You can customize your gift basket with any of our products.",
            'three_col_heading_1' => 'Get Your Gift Basket Delivered Fast!',
            'three_col_heading_2' => 'Order a Gift Basket Today!',
            'three_col_heading_3' => 'Send a Gift Basket Now!',
            'three_col_block_col1' => 'Celebrate your love with a {city} gift basket from {website}! We deliver fast to {city}, {region}, {country} with our same-day, next-day, and other fast delivery methods. Order online now.',
            'three_col_block_col2' => "Buy a {city} gift basket for a friend or loved one! We offer multiple delivery options to {city}, {region}, {country}. Buy online now.",
            'three_col_block_col3' => "Deliver a smile with a {city} gift basket from {website}! We’ll make sure to deliver your gift on time with our fast delivery options, including same-day and next-day delivery to {city}, {region}, {country}. Send a gift basket that will delight anyone on your list.",
            'meta_title' => "{city} {category} Gift Basket & Gift Box Delivery",
            'meta_description' => "Order {category} Gift Baskets Online - FREE delivery to {city} on orders over $100 | We offer gift baskets for all occasions ⏩ Shop Now!",
            'city_page_title' => '%1 Gift Box & Gift Basket',
            'category_page_title' => '%1 %2 Gift Box & Gift Basket',
            'state_page_title' => '%1 Gift Box & Gift Basket',
            'state_page' => [
                'faq_qn_2' => 'How long does it take to deliver a gift basket to %1, %2?'
            ]
        ]
    ];

    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getDefaultValue($field, $storeCode = null)
    {
        if ($storeCode && isset($this->storeSpecificDefaultValues[$storeCode][$field])) {
            return $this->storeSpecificDefaultValues[$storeCode][$field];
        }
        if (isset($this->defaultValues[$field])) {
            return $this->defaultValues[$field];
        }
        return '';
    }

    public function getStatePageDefaultValue($field, $storeCode = null)
    {
        if ($storeCode && isset($this->storeSpecificDefaultValues[$storeCode]['state_page'][$field])) {
            return $this->storeSpecificDefaultValues[$storeCode]['state_page'][$field];
        }
        if ($storeCode && isset($this->storeSpecificDefaultValues[$storeCode][$field])) {
            return $this->storeSpecificDefaultValues[$storeCode][$field];
        }
        if (isset($this->defaultValues['state_page'][$field])) {
            return $this->defaultValues['state_page'][$field];
        }
        if (isset($this->defaultValues[$field])) {
            return $this->defaultValues[$field];
        }
        return '';
    }

    public function getExternalLinksData($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EXTERNAL_LINKS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getAdditionalStyles($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADDITONAL_STYLES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getWebsiteName()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_WEBSITE_NAME,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    public function getCategoryUrlKeyMapping()
    {
        if (empty($this->categoryUrlKeyMappings)) {
            $this->categoryUrlKeyMappings = [];
            $mapping = $this->scopeConfig->getValue(
                self::XML_PATH_CATEGORY_URL_KEY_MAPPING,
                ScopeInterface::SCOPE_STORE
            );
            if (empty($mapping)) {
                return null;
            }
            $mapping = explode(';', $mapping);
            if (count($mapping) > 0) {
                foreach ($mapping as $urlkeyMapping) {
                    if (!empty($urlkeyMapping)) {
                        $this->categoryUrlKeyMappings[] = explode(':', $urlkeyMapping);
                    }
                }
            }
        }

        return $this->categoryUrlKeyMappings;
    }

    public function getCategoryNameMapping()
    {
        if (empty($this->categoryNameMappings)) {
            $mapping = $this->scopeConfig->getValue(
                self::XML_PATH_CATEGORY_NAME_MAPPING,
                ScopeInterface::SCOPE_STORE
            );
            if (empty($mapping)) {
                return null;
            }
            $mapping = explode(';', $mapping);
            if (count($mapping) > 0) {
                foreach ($mapping as $name) {
                    if (!empty($name)) {
                        $this->categoryNameMappings[] = explode(':', $name);
                    }
                }
            }
        }

        return $this->categoryNameMappings;
    }
}
