<?php

declare(strict_types=1);

namespace App\Controller\Application;

use Fuse\Fuse;
use Webmozart\Assert\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Suggestions\TopSearch\TopSearchSuggestionsInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use function array_map;
use function is_string;
use function array_values;

class SuggestionsController extends AbstractController
{
    /**
     * @param iterable<TopSearchSuggestionsInterface> $suggestionResolvers
     */
    public function __construct(
        #[TaggedIterator(tag: TopSearchSuggestionsInterface::class)]
        private iterable $suggestionResolvers,
    )
    {
    }

    #[Route('/top_search', name: 'su_suggestions_top_search', methods: ['GET'])]
    public function topSearch(Request $request, RouterInterface $router): Response
    {
        $search = $request->query->get('q');
        $results = [];

        if (is_string($search) && $search !== '') {
            foreach ($this->suggestionResolvers as $resolver) {
                $name = $resolver->getGroupName();
                $results[$name] = $resolver->getResults($search, $router);
            }
            $results['Pages'] = $this->actions($search);
        }

        return $this->render('app/suggestions/top_search.html.twig', [
            'results' => $results,
        ]);
    }

    /**
     * @return list<array{url: string, name: string}>
     */
    private function actions(string $q): array
    {
        $list = [
            [
                'name' => 'Warehouses',
                'url' => $this->generateUrl('app_warehouses_list'),
            ],
        ];

        $fuse = new Fuse($list, [
            'keys' => ['name'],
        ]);
        $results = $fuse->search($q);

        $values = array_map(static function (array $result) {
            Assert::isArray($item = $result['item'] ?? null);
            Assert::string($name = $item['name'] ?? null);
            Assert::string($url = $item['url'] ?? null);

            return [
                'name' => $name,
                'url' => $url,
            ];
        }, $results);

        return array_values($values);
    }
}
