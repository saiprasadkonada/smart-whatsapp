<aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
        <div class="site-logo">
            @php
               $panel_logo =  $general->panel_logo ?? "panel_logo.png";
               $site_icon =  $general->site_icon ?? "site_icon.png";
            @endphp
            <a href="{{route('user.dashboard')}}">
                <img src="{{showImage(filePath()['panel_logo']['path'].'/'.$panel_logo,filePath()['panel_logo']['size'])}}" alt="{{ translate('Site Logo')}}" class="logo-lg">
                <img src="{{showImage(filePath()['site_logo']['path'].'/'.$site_icon)}}" alt="{{ translate('Site Icon')}}" class="logo-sm">
            </a>
        </div>
        <div class="menu-search-container">
            <input class=" form-control menu-search" placeholder="{{translate('Search Here')}}" type="search" name="" id="searchMenu">
        </div>
    </div>

    <div class="sidebar-menu-container" data-simplebar>
        <ul class="sidebar-menu">

            <li class="sidebar-menu-title" data-text="{{translate('Dashboard')}}">{{ translate('Dashboard')}}</li>

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{request()->routeIs('user.dashboard') ? 'active' : ''}}" href="{{route('user.dashboard')}}">
                    <span><i class="las la-tachometer-alt"></i></span>
                    <p>{{ translate('Overview')}}</p>
                </a>
            </li>

            <li class="sidebar-menu-title" data-text="{{ translate('Membership Management')}}">{{ translate('Membership Management')}}</li>

            @php
                $isMembershipActive = request()->routeIs('user.plan.create', 'user.plan.subscription', 'user.payment.preview', 'user.payment.confirm', 'user.manual.payment.confirm');
            @endphp

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive(['user.plan.create', 'user.payment.preview', 'user.manual.payment.confirm', 'user.payment.confirm'])}}" href="{{route('user.plan.create')}}">
                    <span><i class="las la-money-check-alt"></i></span>
                    <p>{{ translate('Buy Or Renew Plans')}}</p>
                </a>
            </li>

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive('user.plan.subscription')}}" href="{{route('user.plan.subscription')}}">
                    <span><i class="las la-file-invoice-dollar"></i></span>
                    <p>{{ translate('Subscriptions Logs')}}</p>
                </a>
            </li>

            <li class="sidebar-menu-title" data-text="{{translate('Contact Management')}}">{{ translate('Contact Management')}}</li>
            
            @php
                $contactRouteNames = [
                    'user.contact.index',
                    'user.contact.create',
                    'user.contact.settings.index',
                    'user.contact.group.index',
                ];
                $isContactActive = request()->routeIs($contactRouteNames);
            @endphp
            
            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive(['user.contact.group.index'])}}" href="{{route('user.contact.group.index')}}">
                    <span><i class="las la-users"></i></span>
                    <p>{{ translate('Group')}}</p>
                </a>
            </li>

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive(['user.contact.index', 'user.contact.create'])}}" href="{{route('user.contact.index')}}">
                    <span><i class="las la-address-book"></i></span>
                    <p>{{ translate('Contact Details')}}</p>
                </a>
            </li>

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive(['user.contact.settings.index'])}}" href="{{route('user.contact.settings.index')}}">
                    <span><i class="las la-tag"></i></span>
                    <p>{{ translate('Contact Attribute')}}</p>
                </a>
            </li>

            <li class="sidebar-menu-title" data-text="{{translate('Communications Hub')}}">{{ translate('Communications Hub')}}</li>

            @php
                $routeNames = [
                    'user.sms.send',
                    'user.sms.create',
                    'user.sms.search',
                    'user.sms.index',
                ];

                $menuSmsActiveRoute = [
                    'user.campaign.sms'
                ];

                if (request()->route()->type == 'sms') {

                    $menuSmsActiveRoute[1] = 'user.campaign.create';
                    $menuSmsActiveRoute[2] = 'user.campaign.edit';
                }
                $isSmsActive = request()->routeIs($routeNames);

            @endphp
            @php
                $routeNames = [
                    'user.whatsapp.send',
                    'user.whatsapp.create',
                    'user.whatsapp.search',
                    'user.whatsapp.index',
                ];

                $menuWhatsAppActiveRoute = [
                    'user.campaign.whatsapp'
                ];

                if (request()->route()->type == 'whatsapp') {

                    $menuWhatsAppActiveRoute[1] = 'user.campaign.create';
                    $menuWhatsAppActiveRoute[2] = 'user.campaign.edit';
                }
                $isWhatsappActive = request()->routeIs($routeNames);

            @endphp
            @php
                $routeNames = [
                    'user.manage.email.send',
                    'user.manage.email.create',
                    'user.manage.email.search',
                    'user.manage.email.index',
                ];

                $menuEmailActiveRoute = [
                    'user.campaign.email'
                ];

                if (request()->route()->type == 'email') {

                    $menuEmailActiveRoute[1] = 'user.campaign.create';
                    $menuEmailActiveRoute[2] = 'user.campaign.edit';
                }
                $isEmailActive = request()->routeIs($routeNames);

            @endphp

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link collapsed {{$isSmsActive ? 'active' : '' }}" data-bs-toggle="collapse" href="#collapseCommunicationHubSms"
                    role="button" aria-expanded="true" aria-controls="collapseCommunicationHubSms">
                    <span><i class="las la-sms"></i></span>
                    <p>{{translate('SMS Messages')}}  <small><i class="las la-angle-down"></i></small>
                    </p>
                </a>

                <div class="side-menu-dropdown collapse {{$isSmsActive ? 'show' : '' }}"  id="collapseCommunicationHubSms">
                    <ul class="sub-menu">

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('user.sms.send')}}" href="{{route('user.sms.send')}}">
                                <p>{{ translate('Send')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['user.sms.index', 'user.sms.search'])}}" href="{{route('user.sms.index')}}">
                                <p>{{ translate('History')}}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link collapsed {{$isWhatsappActive ? 'active' : '' }}" data-bs-toggle="collapse" href="#collapseCommunicationHubWhatsapp"
                role="button" aria-expanded="true" aria-controls="collapseCommunicationHubWhatsapp">
                <span><i class="lab la-whatsapp"></i></span>
                <p>{{translate('WhatsApp Messages')}}  <small><i class="las la-angle-down"></i></small></p>
                </a>

                <div class="side-menu-dropdown collapse {{$isWhatsappActive ? 'show' : '' }}"  id="collapseCommunicationHubWhatsapp">
                    <ul class="sub-menu">
                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('user.whatsapp.send')}}" href="{{route('user.whatsapp.send')}}">
                                <p>{{ translate('Send')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['user.whatsapp.index', 'user.whatsapp.search'])}}" href="{{route('user.whatsapp.index')}}">
                                <p>{{ translate('History')}}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link collapsed {{$isEmailActive ? 'active' : '' }}" data-bs-toggle="collapse" href="#collapseCommunicationHubEmail"
                role="button" aria-expanded="true" aria-controls="collapseCommunicationHubEmail">
                <span><i class="las la-envelope"></i></span>
                    <p>{{translate('Email Messages')}}<small><i class="las la-angle-down"></i></small>
                    </p>
                </a>

                <div class="side-menu-dropdown collapse {{$isEmailActive ? 'show' : '' }}"  id="collapseCommunicationHubEmail">
                    <ul class="sub-menu">
                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('user.manage.email.send')}}" href="{{route('user.manage.email.send')}}">
                                <p>{{ translate('Send Mail')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['user.manage.email.index', 'user.manage.email.search'])}}" href="{{route('user.manage.email.index')}}">
                                <p>{{ translate('History')}}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="sidebar-menu-title" data-text="{{translate('Marketing & Campaign')}}">{{ translate('Marketing & Campaign')}}</li>

            @php
                $routeNames = [
                    'user.campaign.create',
                    'user.campaign.edit',
                    'user.campaign.sms',
                    'user.campaign.whatsapp',
                    'user.campaign.email',
                ];

                $isCampaignActive = request()->routeIs($routeNames);
            @endphp
            
            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive($menuSmsActiveRoute)}}" href="{{route('user.campaign.sms')}}">
                    <span><i class="fa-solid fa-comment-dots"></i></span>
                    <p>{{ translate('SMS Campaign')}}</p>
                </a>
            </li>

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive($menuWhatsAppActiveRoute)}}" href="{{route('user.campaign.whatsapp')}}">
                    <span><i class="fa-brands fa-whatsapp"></i></span>
                    <p>{{ translate('WhatsApp Campaign')}}</p>
                </a>
            </li>

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive($menuEmailActiveRoute)}}" href="{{route('user.campaign.email')}}">
                    <span><i class="fa-solid fa-envelopes-bulk"></i></span>
                    <p>{{ translate('Email Campaign')}}</p>
                </a>
            </li>

            <li class="sidebar-menu-title" data-text="{{translate('Message Templates')}}">{{ translate('Message Templates')}}</li>

            @php
                $isTemplatesActive = request()->routeIs('user.phone.book.template.index', 'user.template.email.list', 'user.template.email.create', 'user.template.email.edit');
            @endphp

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive('user.phone.book.template.index')}}" href="{{route('user.phone.book.template.index')}}">
                    <span><i class="fa-regular fa-file"></i></span>
                    <p>{{ translate('SMS & WhatsApp')}}</p>
                </a>
            </li>

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive(['user.template.email.list', 'user.template.email.create', 'user.template.email.edit'])}}" href="{{route('user.template.email.list')}}">
                    <span><i class="fa-solid fa-envelope-open-text"></i></span>
                    <p>{{ translate('Email Template')}}</p>
                </a>
            </li>

            <li class="sidebar-menu-title" data-text="{{translate('Communications Gateway')}}">{{ translate('Communications Gateway')}}</li>

            @php
                $isGatewaySettingActive = request()->routeIs('user.mail.gateway.configuration', 'user.mail.edit', 'user.gateway.whatsapp.edit','user.gateway.whatsapp.create', 'user.gateway.whatsapp.cloud.template',
                'user.sms.gateway.sendmethod.api',  'user.sms.gateway.sendmethod.gateway',  'user.gateway.sendmethod.android', 'user.sms.gateway.edit');
            @endphp

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive([ 'user.sms.gateway.sendmethod.api',  'user.sms.gateway.sendmethod.gateway',  'user.gateway.sendmethod.android', 'user.sms.gateway.edit'])}}" href="{{route('user.sms.gateway.sendmethod.gateway')}}">
                    <span><i class="las la-comment-medical"></i></span>
                    <p>{{ translate('SMS')}}</p>
                </a>
            </li>

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link  {{menuActive(['user.gateway.whatsapp.edit','user.gateway.whatsapp.create', 'user.gateway.whatsapp.cloud.template'])}}" href="{{route('user.gateway.whatsapp.create')}}">
                    <span><i class="lab la-whatsapp-square"></i></span>
                    <p>{{ translate('WhatsApp')}}</p>
                </a>
            </li>

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive(['user.mail.gateway.configuration', 'user.mail.edit'])}}" href="{{route('user.mail.gateway.configuration')}}">
                    <span><i class="fa-solid fa-square-envelope"></i></span>
                    <p>{{ translate('Email')}}</p>
                </a>
            </li>

            <li class="sidebar-menu-title" data-text="{{ translate('View Logs & Reports')}}">{{ translate('View Logs & Reports')}}</li>
           

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive(['user.transaction.history', 'user.transaction.search'])}}" href="{{route('user.transaction.history')}}">
                    <span><i class="las la-credit-card"></i></span>
                    <p>{{ translate('Transaction Logs')}}</p>
                </a>
            </li>
            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive(['user.payment.history', 'user.payment.search'])}}" href="{{route('user.payment.history')}}">
                    <span><i class="las la-file-invoice-dollar"></i></span>
                    <p>{{ translate('Payment Log')}}</p>
                </a>
            </li>


            @php
                $isCreditLogsActive = request()->routeIs('user.credit.history', 'user.credit.search', 'user.whatsapp.credit.history', 'user.whatsapp.credit.search', 'user.credit.email.history', 'user.credit.email.search');
            @endphp

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link collapsed {{$isCreditLogsActive  ? 'active' : ''}} " data-bs-toggle="collapse" href="#collapseCreditLogs"
                   role="button" aria-expanded="true" aria-controls="collapseCreditLogs">
                    <span><i class="las la-history"></i></span>
                    <p>{{ translate('Credit Log')}}  <small><i class="las la-angle-down"></i></small>
                    </p>
                </a>

                <div class="side-menu-dropdown collapse {{$isCreditLogsActive  ? 'show' : ''}}"  id="collapseCreditLogs">
                    <ul class="sub-menu">
                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['user.credit.history', 'user.credit.search'])}}" href="{{route('user.credit.history')}}">
                                <p>{{ translate('SMS')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link  {{menuActive(['user.whatsapp.credit.history', 'user.whatsapp.credit.search'])}}" href="{{route('user.whatsapp.credit.history')}}">
                                <p>{{ translate('WhatsApp')}}</p>
                            </a>
                        </li>


                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['user.credit.email.history', 'user.credit.email.search'])}}" href="{{route('user.credit.email.history')}}">
                                <p>{{ translate('Email')}}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>


            <li class="sidebar-menu-title" data-text="{{ translate('SUPPORTS & DEVELOPER OPTIONS')}}">{{ translate('SUPPORTS & DEVELOPER OPTIONS')}}</li>


            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive(['user.ticket.index', 'user.ticket.detail', 'user.ticket.create'])}}" href="{{route('user.ticket.index')}}">
                    <span><i class="las la-ticket-alt"></i></span>
                    <p>{{ translate('Help & Support')}}</p>
                    @if($answered_support_ticket_count > 0)
                        <i class="las la-exclamation sidebar-batch-icon"></i>
                    @endif
                </a>
            </li>

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive('user.generate.api.key')}}" href="{{route('user.generate.api.key')}}">
                    <span><i class="las la-key"></i></span>
                    <p>{{ translate('API Key Generation')}}</p>
                </a>
            </li>


            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive('api.document')}}" href="{{route('api.document')}}">
                    <span><i class="las la-code"></i></span>
                    <p>{{ translate('API Documentation')}}</p>
                </a>
            </li>

        </ul>
    </div>
</aside>

@push('script-push')
    <script>
        (function(){
            "use strict";
            // Sidebar
            const htmlRoot = document.documentElement;
            const mainContent = document.getElementById('mainContent');
            const sidebar = document.querySelector('.sidebar');
            const sidebarControlBtn = document.querySelector('.sidebar-control-btn');
            const sidebarMenuLink = document.querySelectorAll('.sidebar-menu-link');
            const menuTitle = document.querySelectorAll('.sidebar-menu-title');

            // Create Overlay Div
            const overlay = document.createElement('div');
            overlay.classList.add('overlay');

            function handleSidebarToggle() {
                const currentSidebar = htmlRoot.getAttribute('data-sidebar');
                const newAttributes = currentSidebar === 'sm' ? 'lg' : 'sm';
                htmlRoot.setAttribute('data-sidebar', newAttributes);
                mainContent.classList.toggle('added');
                for (const title of menuTitle) {
                    const dataText = title.getAttribute('data-text');
                    title.innerHTML = newAttributes === 'sm' ? '<i class="las la-ellipsis-h"></i>' : dataText;
                }

                sidebarControlBtn.style.cssText = newAttributes === 'sm' ? 'fill: var(--primary-color)' : 'color: var(--text-primary)';
            }

            function handleOverlayClick() {
                overlay.classList.remove('d-block');
                sidebar.classList.remove('active');
            }

            function handleResize() {
                const windowWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
                if (windowWidth <= 991) {
                    htmlRoot.removeAttribute('data-sidebar');
                    sidebar.parentElement.append(overlay);
                    sidebar.classList.remove('active');
                    overlay.classList.remove('d-block');
                    sidebarControlBtn.addEventListener('click', () => {
                        sidebar.classList.add('active');
                        overlay.classList.add('d-block');
                        overlay.addEventListener('click', handleOverlayClick);
                    });
                } else {
                    htmlRoot.setAttribute('data-sidebar','lg');
                    if (document.querySelector('.overlay')) {
                        document.querySelector('.overlay').remove();
                    }
                    if (sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                    }
                    sidebarControlBtn.addEventListener('click', handleSidebarToggle);
                }
            }

            window.addEventListener('resize', handleResize);
            handleResize();

           // Sidebar Menu dropdown collapse
           const menuCollapse =document.querySelectorAll(".sidebar-menu .collapse")
            if (menuCollapse) {
                var collapses = menuCollapse;
                Array.from(collapses).forEach(function (collapse) {
                    // Init collapses
                    var collapseInstance = new bootstrap.Collapse(collapse, {
                        toggle: false,
                    });

  				// Hide sibling collapses on `show.bs.collapse`
				collapse.addEventListener("show.bs.collapse", function (e) {
					e.stopPropagation();
					var closestCollapse = collapse.parentElement.closest(".collapse");
					if (closestCollapse) {
						var siblingCollapses = closestCollapse.querySelectorAll(".collapse");
						Array.from(siblingCollapses).forEach(function (siblingCollapse) {
							var siblingCollapseInstance = bootstrap.Collapse.getInstance(siblingCollapse);
							if (siblingCollapseInstance === collapseInstance) {
								return;
							}
							siblingCollapseInstance.hide();
						});
					} else {
						var getSiblings = function (elem) {
							// Setup siblings array and get the first sibling
							var siblings = [];
							var sibling = elem.parentNode.firstChild;
							// Loop through each sibling and push to the array
							while (sibling) {
								if (sibling.nodeType === 1 && sibling !== elem) {
									siblings.push(sibling);
								}
								sibling = sibling.nextSibling;
							}
							return siblings;
						};
						var siblings = getSiblings(collapse.parentElement);
						Array.from(siblings).forEach(function (item) {
							if (item.childNodes.length > 2)
								item.firstElementChild.setAttribute("aria-expanded", "false");
							var ids = item.querySelectorAll("*[id]");
							Array.from(ids).forEach(function (item1) {
								item1.classList.remove("show");
								if (item1.childNodes.length > 2) {
									var val = item1.querySelectorAll("ul li a");
									Array.from(val).forEach(function (subitem) {
										if (subitem.hasAttribute("aria-expanded"))
											subitem.setAttribute("aria-expanded", "false");
									});
								}
							});
						});
					}
				});

				// Hide nested collapses on `hide.bs.collapse`
				collapse.addEventListener("hide.bs.collapse", function (e) {
					e.stopPropagation();
					var childCollapses = collapse.querySelectorAll(".collapse");
					Array.from(childCollapses).forEach(function (childCollapse) {
						childCollapseInstance = bootstrap.Collapse.getInstance(childCollapse);
						childCollapseInstance.hide();
					});
				});
                });
            }
            $('#searchMenu').keyup(function() {

			var value = $(this).val().toLowerCase();
			$('.sidebar-menu li').each(function() {

				var local = $(this).text().toLowerCase();
                if(local.indexOf(value)>-1) {

                    $(this).show();
                } else {

                    $(this).hide();
                }

			});
		});

        })();
    </script>
@endpush
