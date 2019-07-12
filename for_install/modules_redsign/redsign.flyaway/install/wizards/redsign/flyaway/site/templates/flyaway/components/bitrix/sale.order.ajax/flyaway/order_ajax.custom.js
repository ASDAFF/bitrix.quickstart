(function() {
  'use strict';

  window.RS = window.RS || {};
  window.RS.Sale = window.RS.Sale || {};

  if (!BX.Sale.OrderAjaxComponent) {
    return;
  }

  RS.Sale.OrderAjaxComponent = BX.clone(BX.Sale.OrderAjaxComponent);
  RS.Sale.OrderAjaxComponent.hiddenBlocks = {};
  RS.Sale.OrderAjaxComponent.isInitDeliveryFirstTime = false;

  var activeProperties = {};

  RS.Sale.OrderAjaxComponent.hideNode = function(node) {
    node.classList.add('is-hide');
    this.hiddenBlocks[node.id] = true;
  }

  RS.Sale.OrderAjaxComponent.showNode = function(node) {
    node.classList.remove('is-hide');
    this.hiddenBlocks[node.id] = false;
  }

  RS.Sale.OrderAjaxComponent.isHiddenNode = function(node) {
    return this.hiddenBlocks[node.id];
  }

  RS.Sale.OrderAjaxComponent.getBlockFooter = function() {}
  RS.Sale.OrderAjaxComponent.initFirstSection = function() {}
  RS.Sale.OrderAjaxComponent.clickNextAction = function() {}

  RS.Sale.OrderAjaxComponent.startLoader = function() {
    if (this.BXFormPosting === true) return false;

    this.BXFormPosting = true;

    if (!this.loadingScreen) {
      this.loadingScreen = new BX.PopupWindow("loading_screen", null, {
        overlay: {
          backgroundColor: 'white',
          opacity: '80'
        },
        events: {
          onAfterPopupShow: BX.proxy(function() {
            BX.cleanNode(this.loadingScreen.popupContainer);
            BX.removeClass(this.loadingScreen.popupContainer, 'popup-window');
            rsFlyaway.darken($(this.loadingScreen.popupContainer));
            this.loadingScreen.popupContainer.removeAttribute('style');
            this.loadingScreen.popupContainer.style.display = 'block';
          }, this)
        }
      });
      BX.addClass(this.loadingScreen.popupContainer, 'bx-step-opacity');
    }

    return setTimeout(BX.proxy(function() {
      this.loadingScreen.show()
    }, this), 100);
  }

  RS.Sale.OrderAjaxComponent.endLoader = function() {
    this.BXFormPosting = false;

    if (this.loadingScreen && this.loadingScreen.isShown()) {
      rsFlyaway.darken($(this.loadingScreen.popupContainer));
      this.loadingScreen.close();
    }

    clearTimeout(this.loaderTimer);
  }

  RS.Sale.OrderAjaxComponent.showActualBlock = function(section) {
    var allSections = this.orderBlockNode.querySelectorAll('.bx-soa-section.bx-active'),
      i = 0;
    for (i in allSections) {

      if (!allSections.hasOwnProperty(i)) return;

      this.show(allSections[i]);

      BX.addClass(allSections[i], 'bx-step-completed');
    }
  }

  RS.Sale.OrderAjaxComponent.getPersonTypeControl = function(node) {
    if (!this.result.PERSON_TYPE) return;

    var personTypesCount = BX.util.object_keys(this.result.PERSON_TYPE).length,
      currentType, oldPersonTypeId, i, input, options = [],
      label;

    if (personTypesCount > 1) {
      input = BX.create('DIV', {
        props: {
          className: 'form-group'
        },
        children: [
          BX.create('LABEL', {
            props: {
              className: 'bx-soa-custom-label'
            },
            text: this.params.MESS_PERSON_TYPE
          }), BX.create('BR')
        ]
      });
      node.appendChild(input);
      node = input;
    }

    if (personTypesCount > 2) {
      for (i in this.result.PERSON_TYPE) {
        if (this.result.PERSON_TYPE.hasOwnProperty(i)) {
          currentType = this.result.PERSON_TYPE[i];
          options.push(BX.create('OPTION', {
            props: {
              value: currentType.ID,
              selected: currentType.CHECKED == 'Y'
            },
            text: currentType.NAME
          }));

          if (currentType.CHECKED == 'Y') oldPersonTypeId = currentType.ID;
        }

      }
      node.appendChild(BX.create('SELECT', {
        props: {
          name: 'PERSON_TYPE',
          className: 'form-control '
        },
        children: options,
        events: {
          change: BX.proxy(this.sendRequest, this)
        }
      }));

      this.regionBlockNotEmpty = true;
    } else if (personTypesCount == 2) {
      for (i in this.result.PERSON_TYPE) {
        if (this.result.PERSON_TYPE.hasOwnProperty(i)) {
          currentType = this.result.PERSON_TYPE[i];
          label = BX.create('LABEL', {
            props: {
              className: 'btn btn-default btn-button' + (currentType.CHECKED == 'Y' ? ' active' : ''),
            },
            children: [
              BX.create('INPUT', {
                attrs: {
                  checked: currentType.CHECKED == 'Y'
                },
                props: {
                  type: 'radio',
                  name: 'PERSON_TYPE',
                  value: currentType.ID
                }
              }), currentType.NAME
            ],
            events: {
              change: BX.proxy(this.sendRequest, this)
            }
          });

          node.appendChild(BX.create('DIV', {
            props: {
              className: 'bx-soa-persontypes'
            },
            children: [label]
          }));

          if (currentType.CHECKED == 'Y') oldPersonTypeId = currentType.ID;
        }
      }
      this.regionBlockNotEmpty = true;
    } else {
      for (i in this.result.PERSON_TYPE)
        if (this.result.PERSON_TYPE.hasOwnProperty(i)) node.appendChild(BX.create('INPUT', {
          props: {
            type: 'hidden',
            name: 'PERSON_TYPE',
            value: this.result.PERSON_TYPE[i].ID
          }
        }));
    }

    if (oldPersonTypeId) {
      node.appendChild(
        BX.create('INPUT', {
          props: {
            type: 'hidden',
            name: 'PERSON_TYPE_OLD',
            value: oldPersonTypeId
          }
        }));
    }
  }

  RS.Sale.OrderAjaxComponent.editSection = function(section) {
    if (!section || !section.id) return;

    if (this.result.SHOW_AUTH && section.id != this.authBlockNode.id && section.id != this.basketBlockNode.id) section.style.display = 'none';
    else if (section.id != this.pickUpBlockNode.id) section.style.display = '';

    var titleNode = section.querySelector('.bx-soa-section-title-container'),
      editButton, errorContainer;

    BX.unbindAll(titleNode);
    if (this.result.SHOW_AUTH) {
      BX.bind(titleNode, 'click', BX.proxy(function() {
        this.animateScrollTo(this.authBlockNode);
        this.addAnimationEffect(this.authBlockNode, 'bx-step-good');
      }, this));
    } else {
      BX.bind(titleNode, 'click', BX.proxy(this.showByClick, this));
      editButton = titleNode.querySelector('.bx-soa-editstep');
      editButton && BX.bind(editButton, 'click', BX.proxy(this.showByClick, this));
    }

    errorContainer = section.querySelector('.alert.alert-danger');
    this.hasErrorSection[section.id] = errorContainer && errorContainer.style.display != 'none';
    switch (section.id) {
      case this.authBlockNode.id:
        this.editAuthBlock();
        break;
      case this.basketBlockNode.id:
        this.editBasketBlock(!this.isHiddenNode(section));
        break;
      case this.regionBlockNode.id:
        this.editRegionBlock(!this.isHiddenNode(section));
        break;
      case this.paySystemBlockNode.id:
        this.editPaySystemBlock(!this.isHiddenNode(section));
        break;
      case this.deliveryBlockNode.id:
        if (this.isInitDeliveryFirstTime) {
          this.editDeliveryBlock(!this.isHiddenNode(section));
        } else {
          this.hideNode(section);
          this.editDeliveryBlock(false);
          this.isInitDeliveryFirstTime = true;
        }
        break;
      case this.pickUpBlockNode.id:
        this.hideNode(section);
        this.editPickUpBlock(false);
        break;
      case this.propsBlockNode.id:
        this.editPropsBlock(!this.isHiddenNode(section));
        break;
    }

    section.setAttribute('data-visited', 'true');
  }

  RS.Sale.OrderAjaxComponent.fixLocationsStyle = function(section, hiddenSection) {
    if (!section || !hiddenSection) {
      return;
    }

    var regionActive = this.isHiddenNode(section) ? hiddenSection : section,
      locationSearchInputs, locationStepInputs, i;

    locationSearchInputs = regionActive.querySelectorAll('div.bx-sls div.dropdown-block.bx-ui-sls-input-block');
    locationStepInputs = regionActive.querySelectorAll('div.bx-slst div.dropdown-block.bx-ui-slst-input-block');
    if (locationSearchInputs.length) {
      for (i = 0; i < locationSearchInputs.length; i++) {
        BX.addClass(locationSearchInputs[i], 'form-control');
      }
    }

    if (locationStepInputs.length) {
      for (i = 0; i < locationStepInputs.length; i++) {
        BX.addClass(locationStepInputs[i], 'form-control');
      }

    }
  }

  RS.Sale.OrderAjaxComponent.locationsCompletion = function() {
    var i, locationNode, clearButton, inputStep, inputSearch, arProperty, data, section;

    this.locationsInitialized = true;
    this.fixLocationsStyle(this.regionBlockNode, this.regionHiddenBlockNode);
    this.fixLocationsStyle(this.propsBlockNode, this.propsHiddenBlockNode);

    for (i in this.locations) {
      if (!this.locations.hasOwnProperty(i)) continue;

      locationNode = this.orderBlockNode.querySelector('div[data-property-id-row="' + i + '"]');
      if (!locationNode) continue;

      clearButton = locationNode.querySelector('div.bx-ui-sls-clear');
      inputStep = locationNode.querySelector('div.bx-ui-slst-pool');
      inputSearch = locationNode.querySelector('input.bx-ui-sls-fake[type=text]');

      locationNode.removeAttribute('style');
      this.bindValidation(i, locationNode);
      if (clearButton) {
        BX.bind(clearButton, 'click', function(e) {
          var target = e.target || e.srcElement,
            parent = BX.findParent(target, {
              tagName: 'DIV',
              className: 'form-group'
            }),
            locationInput;

          if (parent) locationInput = parent.querySelector('input.bx-ui-sls-fake[type=text]');

          if (locationInput) BX.fireEvent(locationInput, 'keyup');
        });
      }

      if (!this.firstLoad && this.options.propertyValidation) {
        if (inputStep) {
          arProperty = this.validation.properties[i];
          data = this.getValidationData(arProperty, locationNode);
          section = BX.findParent(locationNode, {
            className: 'bx-soa-section'
          });

          if (section && section.getAttribute('data-visited') == 'true') this.isValidProperty(data);
        }

        if (inputSearch) BX.fireEvent(inputSearch, 'keyup');
      }

      if (this.isHiddenNode(this.regionBlockNode)) this.editFadeRegionContent(this.regionBlockNode.querySelector('.bx-soa-section-content'));
    }

    if (this.firstLoad && !this.result.LAST_ORDER_DATA.FAIL) this.showActualBlock();

    this.checkNotifications();
  }

  RS.Sale.OrderAjaxComponent.editFadePropsContent = function(node) {
    if (!node)
      return;

    var errorNode = this.propsHiddenBlockNode.querySelector('.alert'),
      personType = this.getSelectedPersonType(),
      fadeParamName, props,
      group, property, groupIterator, propsIterator, i, validPropsErrors;

    BX.cleanNode(node);

    if (errorNode)
      node.appendChild(errorNode.cloneNode(true));

    if (personType) {
      fadeParamName = 'PROPS_FADE_LIST_' + personType.ID;
      props = this.params[fadeParamName];
    }

    if (!props || props.length == 0) {
      node.innerHTML += '<strong>' + BX.message('SOA_ORDER_PROPS') + '</strong>';
    } else {
      groupIterator = this.fadedPropertyCollection.getGroupIterator();
      while (group = groupIterator()) {
        propsIterator = group.getIterator();
        while (property = propsIterator()) {
          for (i = 0; i < props.length; i++)
            if (props[i] == property.getId() && property.getSettings()['IS_ZIP'] != 'Y')
              this.getPropertyRowNode(property, node, true);
        }
      }
    }

    if (this.propsBlockNode.getAttribute('data-visited') == 'true') {
      validPropsErrors = this.isValidPropertiesBlock();
      if (validPropsErrors.length)
        this.showError(this.propsBlockNode, validPropsErrors);
    }

    BX.bind(node.querySelector('.alert.alert-danger'), 'click', BX.proxy(this.showByClick, this));
    BX.bind(node.querySelector('.alert.alert-warning'), 'click', BX.proxy(this.showByClick, this));
  }

  RS.Sale.OrderAjaxComponent.show = function(node) {
    if (!node || !node.id || !this.isHiddenNode(node)) return;

    this.showNode(node);
    BX.removeClass(node, 'bx-step-error bx-step-warning');

    switch (node.id) {
      case this.authBlockNode.id:
        this.authBlockNode.style.display = '';
        BX.addClass(this.authBlockNode, 'bx-active');
        break;
      case this.basketBlockNode.id:
        this.editActiveBasketBlock(true);
        this.alignBasketColumns();
        break;
      case this.regionBlockNode.id:
        this.editActiveRegionBlock(true);
        break;
      case this.deliveryBlockNode.id:
        this.editActiveDeliveryBlock(true);
        break;
      case this.paySystemBlockNode.id:
        this.editActivePaySystemBlock(true);
        break;
      case this.pickUpBlockNode.id:
        this.editActivePickUpBlock(true);
        break;
      case this.propsBlockNode.id:
        this.editActivePropsBlock(true);
        break;
    }

    if (node.getAttribute('data-visited') === 'false') this.showBlockErrors(node);

    node.setAttribute('data-visited', 'true');
    BX.removeClass(node, 'bx-step-completed');
  }

  RS.Sale.OrderAjaxComponent.fade = function(node) {
    BX.Sale.OrderAjaxComponent.fade.call(this, node);
    this.hideNode(node);
  }

  RS.Sale.OrderAjaxComponent.showByClick = function(event) {
    var target = event.target || event.srcElement,
      node = $(event.target).closest('.bx-soa-section')[0],
      scrollTop = BX.GetWindowScrollPos().scrollTop;

    if (this.isHiddenNode(node)) {
      this.show(node);
      this.cleanHideSection(node);
    } else {
      this.fade(node);
    }

    this.reachGoal('edit', node);
    return BX.PreventDefault(event);
  }

  RS.Sale.OrderAjaxComponent.cleanHideSection = function(node) {
    var hiddenNode = null;

    switch (node.id) {
      case this.regionBlockNode.id:
        hiddenNode = this.regionHiddenBlockNode;
        break;
      case this.deliveryBlockNode.id:
        hiddenNode = this.deliveryHiddenBlockNode;
        break;
      case this.paySystemBlockNode.id:
        hiddenNode = this.paySystemHiddenBlockNode;
        break;
      case this.propsBlockNode.id:
        hiddenNode = this.propsHiddenBlockNode;
        break;
    }

    BX.cleanNode(hiddenNode);
  }

  RS.Sale.OrderAjaxComponent.editCoupons = function(basketItemsNode) {
    var sectionNode = basketItemsNode.parentNode;
    var couponsList = this.getCouponsList(true),
      couponsLabel = this.getCouponsLabel(true),
      couponsBlock = BX.create('DIV', {
        props: {
          className: 'bx-soa-coupon-block'
        },
        children: [
          BX.create('DIV', {
            props: {
              className: 'bx-soa-coupon-input'
            },
            children: [
              BX.create('INPUT', {
                props: {
                  id: 'coupon__' + sectionNode.id,
                  className: 'form-control bx-ios-fix',
                  type: 'text'
                },
                events: {
                  change: BX.proxy(function() {
                    var newCoupon = BX('coupon__' + sectionNode.id);
                    if (newCoupon && newCoupon.value) this.sendRequest('enterCoupon', newCoupon.value);
                  }, this)
                }
              })
            ]
          }), BX.create('SPAN', {
            props: {
              className: 'bx-soa-coupon-item'
            },
            children: couponsList
          })
        ]
      });

    basketItemsNode.appendChild(
      BX.create('DIV', {
        props: {
          className: 'bx-soa-coupon'
        },
        children: [
          couponsLabel, couponsBlock
        ]
      }));
  }

  RS.Sale.OrderAjaxComponent.selectStore = function(event) {
    var target = event.target || event.srcElement,
      currentSection = BX.findParent(target, {
        className: "bx-soa-section"
      });

    BX.Sale.OrderAjaxComponent.selectStore.call(this, event);
    target = event.target || event.srcElement;

    if (target.tagName == 'A') {
      this.hideNode(currentSection);
      this.fade(currentSection);
    }

    return BX.PreventDefault(event);
  }

  RS.Sale.OrderAjaxComponent.editAuthorizeForm = function(authContent) {
    var login, password, remember, button, authFormNode;

    login = this.createAuthFormInputContainer(
      BX.message('STOF_LOGIN'),
      BX.create('INPUT', {
        attrs: {
          'data-next': 'USER_PASSWORD'
        },
        props: {
          name: 'USER_LOGIN',
          type: 'text',
          value: this.result.AUTH.USER_LOGIN,
          maxlength: "30"
        },
        events: {
          keypress: BX.proxy(this.checkKeyPress, this)
        }
      })
    );
    password = this.createAuthFormInputContainer(
      BX.message('STOF_PASSWORD'),
      BX.create('INPUT', {
        attrs: {
          'data-send': true
        },
        props: {
          name: 'USER_PASSWORD',
          type: 'password',
          value: '',
          maxlength: "30"
        },
        events: {
          keypress: BX.proxy(this.checkKeyPress, this)
        }
      })
    );
    remember = BX.create('DIV', {
      props: {
        className: 'bx-authform-formgroup-container'
      },
      children: [
        BX.create('DIV', {
          props: {
            className: 'gui-box'
          },
          children: [
            BX.create('LABEL', {
              props: {
                className: 'gui-checkbox'
              },
              children: [
                BX.create('INPUT', {
                  props: {
                    type: 'checkbox',
                    name: 'USER_REMEMBER',
                    value: 'Y',
                    className: "gui-checkbox-input"
                  }
                }),
                BX.create('SPAN', {
                  props: {
                    className: "gui-checkbox-icon"
                  }
                }),
                BX.create('SPAN', {
                  text: BX.message('STOF_REMEMBER')
                })
              ]
            })
          ]
        })
      ]
    });
    // remember = BX.create('DIV', {
    //   props: {
    //     className: 'bx-authform-formgroup-container'
    //   },
    //   children: [
    //     BX.create('DIV', {
    //       props: {
    //         className: 'checkbox'
    //       },
    //       children: [
    //         BX.create('LABEL', {
    //           props: {
    //             className: 'bx-filter-param-label'
    //           },
    //           children: [
    //             BX.create('INPUT', {
    //               props: {
    //                 type: 'checkbox',
    //                 name: 'USER_REMEMBER',
    //                 value: 'Y'
    //               }
    //             }),
    //             BX.create('SPAN', {
    //               props: {
    //                 className: 'bx-filter-param-text'
    //               },
    //               text: BX.message('STOF_REMEMBER')
    //             })
    //           ]
    //         })
    //       ]
    //     })
    //   ]
    // });
    button = BX.create('DIV', {
      props: {
        className: 'bx-authform-formgroup-container'
      },
      children: [
        BX.create('INPUT', {
          props: {
            id: 'do_authorize',
            type: 'hidden',
            name: 'do_authorize',
            value: 'N'
          }
        }),
        BX.create('INPUT', {
          props: {
            type: 'submit',
            className: 'btn btn-lg btn-default',
            value: BX.message('STOF_ENTER')
          },
          events: {
            click: BX.proxy(function(e) {
              BX('do_authorize').value = 'Y';
              this.sendRequest('showAuthForm');
              return BX.PreventDefault(e);
            }, this)
          }
        })
      ]
    });
    authFormNode = BX.create('DIV', {
      props: {
        className: 'bx-authform'
      },
      children: [
        BX.create('H3', {
          props: {
            className: 'bx-title'
          },
          text: BX.message('STOF_AUTH_REQUEST')
        }),
        login,
        password,
        remember,
        button,
        BX.create('A', {
          props: {
            href: this.params.PATH_TO_AUTH + '?forgot_password=yes&back_url=' + encodeURIComponent(document.location.href)
          },
          text: BX.message('STOF_FORGET_PASSWORD')
        })
      ]
    });

    authContent.appendChild(BX.create('DIV', {
      props: {
        className: 'col-md-6'
      },
      children: [authFormNode]
    }));
  }

  RS.Sale.OrderAjaxComponent.isValidProperty = function(data, fieldName) {
    var propErrors = [],
      inputErrors, i;

    if (!data || !data.inputs)
      return propErrors;

    for (i = 0; i < data.inputs.length; i++) {
        console.log(activeProperties)
      if (activeProperties[data.inputs[i].id]) {
        inputErrors = data.func(data.inputs[i], !!fieldName);
        if (inputErrors.length)
          propErrors[i] = inputErrors.join('<br />');
      }
    }

    this.showValidationResult(data.inputs, propErrors);

    return propErrors;
  }

  RS.Sale.OrderAjaxComponent.bindValidation = function(id, propContainer) {
      if (!this.validation.properties || !this.validation.properties[id])
        return;

      var arProperty = this.validation.properties[id],
        data = this.getValidationData(arProperty, propContainer),
        i, k;

      if (data && data.inputs && data.action) {
        for (i = 0; i < data.inputs.length; i++) {
          if (BX.type.isElementNode(data.inputs[i])) {
            BX.bind(data.inputs[i], data.action, BX.proxy(function(e) {
              activeProperties[e.target.id] = true;
              this.isValidProperty(data);
            }, this));

          } else {
            for (k = 0; k < data.inputs[i].length; k++)
              BX.bind(data.inputs[i][k], data.action, BX.proxy(function() {
                this.isValidProperty(data);
              }, this));
          }
        }
      }
    }

}());
