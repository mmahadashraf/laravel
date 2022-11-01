<?php


namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;

class MenuController extends BaseController
{
    /* TODO: complete getMenuItems so that it returns a nested menu structure from the database
    Requirements
    - the eloquent expressions should result in EXACTLY one SQL query no matter the nesting level or the amount of menu items.
    - post process your results in PHP
    - it should work for infinite level of depth (children of childrens children of childrens children, ...)
    - verify your solution with `php artisan test`
    - do a `git commit && git push` after you are done or when the time limit is over

    Hints:
    - imagine a maximum of a few hundred menu items
    - partial or not working answers also get graded so make sure you commit what you have

    Sample response on GET /menu:
    ```json
    [
        {
            "id": 1,
            "name": "All events",
            "url": "/events",
            "parent_id": null,
            "created_at": "2021-04-27T15:35:15.000000Z",
            "updated_at": "2021-04-27T15:35:15.000000Z",
            "children": [
                {
                    "id": 2,
                    "name": "Laracon",
                    "url": "/events/laracon",
                    "parent_id": 1,
                    "created_at": "2021-04-27T15:35:15.000000Z",
                    "updated_at": "2021-04-27T15:35:15.000000Z",
                    "children": [
                        {
                            "id": 3,
                            "name": "Illuminate your knowledge of the laravel code base",
                            "url": "/events/laracon/workshops/illuminate",
                            "parent_id": 2,
                            "created_at": "2021-04-27T15:35:15.000000Z",
                            "updated_at": "2021-04-27T15:35:15.000000Z",
                            "children": []
                        },
                        {
                            "id": 4,
                            "name": "The new Eloquent - load more with less",
                            "url": "/events/laracon/workshops/eloquent",
                            "parent_id": 2,
                            "created_at": "2021-04-27T15:35:15.000000Z",
                            "updated_at": "2021-04-27T15:35:15.000000Z",
                            "children": []
                        }
                    ]
                },
                {
                    "id": 5,
                    "name": "Reactcon",
                    "url": "/events/reactcon",
                    "parent_id": 1,
                    "created_at": "2021-04-27T15:35:15.000000Z",
                    "updated_at": "2021-04-27T15:35:15.000000Z",
                    "children": [
                        {
                            "id": 6,
                            "name": "#NoClass pure functional programming",
                            "url": "/events/reactcon/workshops/noclass",
                            "parent_id": 5,
                            "created_at": "2021-04-27T15:35:15.000000Z",
                            "updated_at": "2021-04-27T15:35:15.000000Z",
                            "children": []
                        },
                        {
                            "id": 7,
                            "name": "Navigating the function jungle",
                            "url": "/events/reactcon/workshops/jungle",
                            "parent_id": 5,
                            "created_at": "2021-04-27T15:35:15.000000Z",
                            "updated_at": "2021-04-27T15:35:15.000000Z",
                            "children": []
                        }
                    ]
                }
            ]
        }
    ]
     */

    /**
     * Gets all menu items with their all recursive children
     *
     * @return array
     */
    public function getMenuItems(): array
    {
        $all_menu_items = MenuItem::all();
        if (!$all_menu_items->isEmpty()) {
            // go through each menu item and fetch its children recursively

            $all_menu_items->transform(function ($menu_item) use ($all_menu_items) {
                if(empty($menu_item->parent_id)){
                    $menu_item->children = $this->recursiveChildren($menu_item, $all_menu_items);
                }
                return $menu_item;
            });
        }

        // Remove all menu items which are not a root level menu
        // as they are already in a children tree of their root level menu
        //return as array as expected by the test

        return $all_menu_items->reject(function ($item){
            return !empty($item->parent_id);
        })->toArray();
    }

    /**
     * Recursively gets all depth children of a menu item
     *
     * @param MenuItem $menu_item
     * @param Collection $all_menu_items
     * @return array
     */
    private function recursiveChildren(MenuItem $menu_item, Collection $all_menu_items): array
    {
        $items = [];
        foreach($all_menu_items as $local_menu_item){
            // if sent menu item is the parent of current menu item in loop then get its children

            if($menu_item->id == $local_menu_item->parent_id){
                $local_menu_item->children = $this->recursiveChildren($local_menu_item, $all_menu_items);
                $items[] = $local_menu_item;
            }
        }
        return $items;
    }
}
