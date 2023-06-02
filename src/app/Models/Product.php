<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use JpnForPhp\Transliterator\Transliterator;
use JpnForPhp\Transliterator\System\Hepburn;

class Product extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'image',
        'description',
        'price',
        'image_mime',
        'image_size',
        'created_by',
        'updated_by'
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function ($model) {

                $slug = str_replace([" ", "\n"], "-", $model->title);

                //$model->titleがひらがなであればローマ字に変換
                if (!preg_match('/\A[a-z\-]+\z/i', $slug)) {
                    // goo APIのエンドポイント
                    $url = 'https://labs.goo.ne.jp/api/hiragana';

                    // リクエストパラメータ
                    $data = [
                        'app_id' => env('GOO_APP_ID'), // app_id
                        'sentence' => $model->title, // 変換したい文章
                        'output_type' => 'hiragana' // 出力タイプ
                    ];

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json'
                    ]);

                    $response = curl_exec($ch);

                    curl_close($ch);

                    $response = json_decode($response, true);

                    // レスポンスからひらがなを取得
                    $hiraganaTitle = $response['converted'];

                    // ひらがなのタイトルをローマ字に変換
                    $transliterator = new Transliterator();
                    $transliterator->setSystem(new Hepburn());
                    $slug = $transliterator->transliterate($hiraganaTitle);
                }

                return $slug;
            })
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
