# TYPO3 - Page Layout Form Element
This package adds a new form element preset to your TYPO3 TCA configuration, which allows
you to add content elements to every record. It utilizes hidden pages to provide you a complete 
page layout GUI.


## Requirements

- TYPO3 v9
- TYPO3 - Better API
- Installation using Composer

## Installation
Install this package using composer:

```
composer require labor-digital/typo3-page-layout-form-element
```

## Usage

#### Configuring the TCA
You should read up on creating TCA definitions using the Better API bundle to use this package.

After that go to your table configuration class and add a new field using the pageLayout() form preset:

```php
<?php

use LaborDigital\Typo3BetterApi\BackendForms\TcaForms\TcaTable;use LaborDigital\Typo3BetterApi\ExtConfig\ExtConfigContext;use LaborDigital\Typo3BetterApi\ExtConfig\Option\Table\TableConfigurationInterface;

class YourTable implements TableConfigurationInterface
{
    /**
    * @inheritDoc
    */
    public static function configureTable(TcaTable $table,ExtConfigContext $context,bool $isOverride) : void{
        $table->getField('content')
              ->applyPreset()->pageLayout();
    }
        
}
```

#### Adding content elements
After adding the preset, make sure to clear the caches, update your SQL Database and open the record.
There you should find a new form section that tells you that "you have to save the record in order before you can edit the contents".
When you save the record the content-page will automatically be created for you. 

When the page reloads after saving, you will see a button labeled "edit contents" which leads
you to the page layout you can use to add any number content elements including grid elements or complex plugins.

#### Extending the Model
To use the content elements in your frontend you should probably start with adding a new 
property to your records model. 

```php
<?php

namespace LaborDigital\YourExt\Domain\Model;

use LaborDigital\Typo3BetterApi\Domain\Model\BetterEntity;
use LaborDigital\Typo3PageLayoutFormElement\Domain\Model\PageLayoutContent;
use LaborDigital\Typo3PageLayoutFormElement\Domain\Model\PageLayoutContentModelTrait;

class MagazineArticle extends BetterEntity
{
    // Use the model helper trait
    use PageLayoutContentModelTrait;
    
    /**
     * This field will hold the numeric UID of the linked content page
     * @var int
     */
    protected $content;

    /**
     * This method will return the object representation of 
     * your Page layout contents. You can use it to get the record information as array
     * or render it to HTML directly.
     * @return \LaborDigital\Typo3PageLayoutFormElement\Domain\Model\PageLayoutContent
     */
    public function getContent(): PageLayoutContent
    {
        return $this->getPageLayoutContentObject('content');
    }
}
```

#### Rendering the contents in FLUID (optional)
After you extended your model you can use the built-in viewhelper to
render the content elements in fluid. For that pass the instance 
of your model into your fluid view using ```$view->assign('yourModel', $model);```

And then extend your template like so:

```html
<!-- Add the viewhelper namespace -->
{namespace pageLayout=LaborDigital\Typo3PageLayoutFormElement\ViewHelpers}

<!-- Render the content elements as HTML -->
<pageLayout:PageLayoutContent field="{yourModel.content}"/>
			
```

#### Using [Frontend API](https://github.com/labor-digital/typo3-frontend-api)
The page layout form element comes with built-in support for the frontend api extension.
You don't have to take care of anything, just create the resource for your model
and make sure you add the "?include=content" parameter to your query when requiring the records
from the API and you are all set.

#### using [Vue Framework](https://github.com/labor-digital/typo3-vue-framework)
As the official framework to our frontend API bundle, the vue-framework also has built-in support
for the page layout form element. After you added the resource of your model
you can either load your record using the initial state query or via the resource api.

In your vue component you can then handle the layout like any other data.
Let's say you have a content element controller like this, that defines
a initial state query for a single element:

```php
<?php
use LaborDigital\Typo3FrontendApi\ContentElement\Controller\AbstractContentElementController;
use LaborDigital\Typo3FrontendApi\ContentElement\Controller\ContentElementControllerContext;

abstract class DummyContentElement extends AbstractContentElementController
{ 
    public function handle(ContentElementControllerContext $context): void
    {
        $context->setInitialStateQuery('myModel', [
            'id'      => $context->getRequest()->getQueryParams()['id'],
            'include' => ['content'],
        ]);
    }
}
```

Now you can do the following in your vue component to render the content element list:

```vue
<template>
    <div>
        <!-- Use the content-element-children component to render your layout elements -->
        <content-element-children :children="context.initialState.get('content.children', [])"/>
    </div>
</template>

<script lang="ts">
    import {ContentElementContext} from '@labor-digital/typo3-vue-framework/lib/Core/Context/ContentElementContext';

    export default {
        name: 'Dummy',
        props: {
            // The context gets automatically injected
            context: null as ContentElementContext
        },
    };
</script>
```

## Postcardware
You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: LABOR.digital - Fischtorplatz 21 - 55116 Mainz, Germany

We publish all received postcards on our [company website](https://labor.digital). 
