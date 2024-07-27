<?php

namespace App\Filament\Marketing\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsDigitalMarketingOverview extends BaseWidget
{
    use HasWidgetShield;
    protected static ?string $pollingInterval = '10s';
    protected function getStats(): array
    {
        $count_about_total_isactive = \App\Models\Marketing\Digital\AboutUs::where('active', true)->count();
        $count_about_total = \App\Models\Marketing\Digital\AboutUs::count();
        $count_article_total_isactive = \App\Models\Marketing\Digital\Article::where('active', true)->count();
        $count_article_total = \App\Models\Marketing\Digital\Article::count();
        $count_article_category_total = \App\Models\Marketing\Digital\ArticleCategory::count();
        $count_award_total_isactive = \App\Models\Marketing\Digital\AwardItem::where('active', true)->count();
        $count_certificate_total_isactive = \App\Models\Marketing\Digital\CertificateItem::where('active', true)->count();
        $count_carousel_total_isactive = \App\Models\Marketing\Digital\Carousel::where('active', true)->count();
        $count_contact_total_isactive = \App\Models\Marketing\Digital\Contact::count();
        $count_meta_total_isactive = \App\Models\Marketing\Digital\Meta::count();
        $count_product_total = \App\Models\Marketing\Digital\ProductItem::count();
        $count_product_total_isactive = \App\Models\Marketing\Digital\ProductItem::where('active', true)->count();
        return [
            Stat::make('About Us Data', "$count_about_total All Data")
                ->description("$count_about_total_isactive Total Data")
                ->descriptionIcon('heroicon-o-information-circle'),
            Stat::make('Article Data', "$count_article_total All Data")
                ->description("$count_article_total_isactive Total Data")
                ->descriptionIcon('heroicon-o-information-circle'),
            Stat::make('Article Category Data', "$count_article_category_total All Data")
                ->description("$count_product_total_isactive Total Data")
                ->descriptionIcon('heroicon-o-information-circle'),
            Stat::make('Product Data', "$count_product_total All Data")
                ->description("$count_meta_total_isactive Total Data")
                ->descriptionIcon('heroicon-o-information-circle'),
            Stat::make('Awards Data', "$count_award_total_isactive All Data")
                ->description("$count_award_total_isactive Total Data")
                ->descriptionIcon('heroicon-o-information-circle'),
            Stat::make('Certificates Data', "$count_certificate_total_isactive All Data")
                ->description("$count_certificate_total_isactive Total Data")
                ->descriptionIcon('heroicon-o-information-circle'),
            Stat::make('Carousels Data', "$count_carousel_total_isactive All Data")
                ->description("$count_carousel_total_isactive Total Data")
                ->descriptionIcon('heroicon-o-information-circle'),
            Stat::make('Contacts Data', "$count_contact_total_isactive All Data")
                ->description("$count_contact_total_isactive Total Data")
                ->descriptionIcon('heroicon-o-information-circle'),
            Stat::make('Meta Data(SEO SETUP)', "$count_meta_total_isactive All Data")
                ->description("$count_meta_total_isactive Total Data")
                ->descriptionIcon('heroicon-o-information-circle'),
        ];
    }
}
