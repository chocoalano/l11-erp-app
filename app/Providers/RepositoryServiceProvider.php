<?php

namespace App\Providers;

// interface
use App\Interfaces\Hris\AttendanceInterface;
use App\Interfaces\Hris\AuthenticationInterface;
use App\Interfaces\Hris\JobPositionInterface;
use App\Interfaces\Hris\OrganizationInterface;
use App\Interfaces\Hris\UserInterface;

use App\Interfaces\Marketing\AboutUsInterface;
use App\Interfaces\Marketing\ArticleInterface;
use App\Interfaces\Marketing\AwardInterface;
use App\Interfaces\Marketing\CarouselInterface;
use App\Interfaces\Marketing\CertificateInterface;
use App\Interfaces\Marketing\MetaSeoInterface;
use App\Interfaces\Marketing\PartnerInterface;
use App\Interfaces\Marketing\ProductInterface;
use App\Interfaces\Marketing\ReasonInterface;
use App\Interfaces\Marketing\SosmedInterface;
use App\Interfaces\Marketing\ValuesInterface;
// repo
use App\Repositories\Hris\AttendanceRepository;
use App\Repositories\Hris\AuthenticationRepository;
use App\Repositories\Hris\JobPositionRepository;
use App\Repositories\Hris\OrganizationRepository;
use App\Repositories\Hris\UserRepository;
use App\Repositories\Marketing\AboutUsRepository;
use App\Repositories\Marketing\ArticleRepository;
use App\Repositories\Marketing\AwardRepository;
use App\Repositories\Marketing\CarouselRepository;
use App\Repositories\Marketing\CertificateRepository;
use App\Repositories\Marketing\MetaSeoRepository;
use App\Repositories\Marketing\PartnerRepository;
use App\Repositories\Marketing\ProductRepository;
use App\Repositories\Marketing\ReasonRepository;
use App\Repositories\Marketing\SosmedRepository;
use App\Repositories\Marketing\ValuesRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AuthenticationInterface::class,AuthenticationRepository::class);
        $this->app->bind(AttendanceInterface::class,AttendanceRepository::class);
        $this->app->bind(UserInterface::class,UserRepository::class);
        $this->app->bind(OrganizationInterface::class,OrganizationRepository::class);
        $this->app->bind(JobPositionInterface::class,JobPositionRepository::class);
        $this->app->bind(AboutUsInterface::class,AboutUsRepository::class);
        $this->app->bind(CarouselInterface::class,CarouselRepository::class);
        $this->app->bind(ValuesInterface::class,ValuesRepository::class);
        $this->app->bind(ProductInterface::class,ProductRepository::class);
        $this->app->bind(ReasonInterface::class,ReasonRepository::class);
        $this->app->bind(ArticleInterface::class,ArticleRepository::class);
        $this->app->bind(AwardInterface::class,AwardRepository::class);
        $this->app->bind(CertificateInterface::class,CertificateRepository::class);
        $this->app->bind(PartnerInterface::class,PartnerRepository::class);
        $this->app->bind(MetaSeoInterface::class,MetaSeoRepository::class);
        $this->app->bind(SosmedInterface::class,SosmedRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
