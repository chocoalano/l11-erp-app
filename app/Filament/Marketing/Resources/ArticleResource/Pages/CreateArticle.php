<?php

namespace App\Filament\Marketing\Resources\ArticleResource\Pages;

use App\Filament\Marketing\Resources\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;
}
