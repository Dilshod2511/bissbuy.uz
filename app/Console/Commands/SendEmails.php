<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
          $products = \App\Models\Product::whereNull('variants')->get();
            foreach ($products as $product) {
               $variants = [];
                $images = [];
                $recors = \DB::table('product_images')->where('product_id', $product->id)->get();
                foreach ($recors as $image) {
                    $images[] = [
                        'path' => $image->product_image,
                        'mime_type' => 'image/jpeg'
                    ];
                }
        
              
            
                $variants[]  = [
                    'description' => $product->short_description,
                    'price' => $product->product_price,
                    'images' => $images,
                    'options' => []
                ];
        
               // $keywords = $product->key_words;
                //$imploded = explode(',', $keywords);
        
                $product->variants = json_encode($variants);
               // $product->tags = $imploded;
                $product->save();
                echo $product->id . "\n";
            }

    }
}
