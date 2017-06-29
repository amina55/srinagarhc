<?php 
$loggedInUser = Auth::user();
$name   = ($loggedInUser->shopname) ? $loggedInUser->shopname.' ('.$loggedInUser->username.')' : $loggedInUser->username;
?>
<header>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <a href="{{ route('default-route') }}" class="mt10 logo pull-left no-text-decoration">                    
                    <img src="/images/srr_logo.png" />
                </a>
                <div class="dropdown pull-right">
                    <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ trans('content.hello').' '. $name. '!!' }}
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dLabel">
                        <li>
                            <a href="{{ route('logout') }}">{{ trans('content.logout') }}</a>
                        </li>                       
                    </ul>
                </div>

                @if($locales)
                    <div class="dropdown pull-right mr20">
                        <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ $locales[session('locale')] }}
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dLabel">
                            <?php unset($locales[session('locale')]); ?>
                            @foreach($locales as $locale => $title)
                            <li>
                                <a href="{{ route('change-locale', $locale) }}">{{ $title }}</a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </div>            
        </div>
    </div>

</header>
<div class="ml20">
    @if(Auth::user()->user_type == 'admin')
        <a class="btn btn-medium btn-green" href="{{ route('admin.shops-list') }}">{{ trans('content.manage_shops') }}</a>
        <a class="btn btn-medium btn-green ml10">{{ trans('content.manage_templates') }}</a>
        <a class="btn btn-medium btn-green ml10">{{ trans('content.configurations') }}</a>
        <a class="btn btn-medium btn-green ml10">{{ trans('content.manage_categories') }}</a>
        <a class="btn btn-medium btn-green ml10">{{ trans('content.manage_languages') }}</a>
    @elseif(Auth::user()->user_type == 'shop')
        <a class="btn btn-medium btn-green" href="{{ route('list-projects') }}">{{ trans('content.manage_customer_segments') }}</a>
    @endif
</div>