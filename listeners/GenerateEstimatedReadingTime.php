<?php
namespace App\Listeners;

use TightenCo\Jigsaw\Jigsaw;

class GenerateEstimatedReadingTime
{
    public function handle(Jigsaw $jigsaw)
    {
        foreach(['posts', 'dev', 'archive'] as $collection)
        {
            $jigsaw->getCollection($collection)->map(function ($post)
            {
                $post->estimated_reading_time = $this->getEstimatedReadingTime($post);
            });
        }
    }

    private function getEstimatedReadingTime($post, $wpm = 260)
    {
        $wordCount = str_word_count(strip_tags($post));

        $minutes = (int) ceil($wordCount / $wpm);

        return $minutes . ' min read';
    }
}