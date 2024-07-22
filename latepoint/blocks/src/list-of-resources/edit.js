/**
 * External dependencies
 */
import styled from '@emotion/styled';

/**
 * WordPress components that create the necessary UI elements for the block
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-components/
 */
import {registerBlockType} from '@wordpress/blocks';
import {__} from '@wordpress/i18n';
import {
  TextControl,
  Button,
  SelectControl,
  ToggleControl,
  FontSizePicker,
  ColorIndicator,
  Dropdown,
  DropdownContentWrapper,
  ColorPalette,
  Flex,
  FlexBlock,
  __experimentalGrid as Grid,
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOption as ToggleGroupControlOption
} from '@wordpress/components';

import {
  __experimentalBoxControl as BoxControl,
  __experimentalToolsPanel as ToolsPanel,
  __experimentalToolsPanelItem as ToolsPanelItem,
  __experimentalUnitControl as UnitControl,
} from '@wordpress/components';

import {Panel, PanelBody, PanelRow} from '@wordpress/components';
import {useState} from '@wordpress/element';


/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import {
  useBlockProps,
  InspectorControls,
} from '@wordpress/block-editor';

const ListOfResourcesWrapper = styled.div`

`;

const LatepointBookButton = styled.div`
  padding: 9px 14px;
  background-color: #2d54de;
  color: #fff;
  border-radius: 4px;
  font-weight: bold;
  display: inline-block;
  margin-top: 10px;
  `;

const ColorAttributesWrapper = styled.div`
  margin-bottom: 15px;
  border: 1px solid #eee;
`;

const ListOfResources = styled.div`
  display: grid;
  gap: 30px;
  grid-template-columns: 1fr 1fr 1fr 1fr;

  &.resources-columns-1 {
    grid-template-columns: 1fr;
    grid-gap: 20px;
  }
  
  &.resources-columns-2 {
    grid-template-columns: 1fr 1fr;
    grid-gap: 50px;
  }

  &.resources-columns-3 {
    grid-template-columns: 1fr 1fr 1fr;
    grid-gap: 40px;
  }

  &.resources-columns-4 {
    grid-template-columns: 1fr 1fr 1fr 1fr;
    grid-gap: 30px;
  }

  &.resources-columns-5 {
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
    grid-gap: 20px;
  }
`;

const Resource = styled.div`
  padding: 20px;
  background-color: #fff;
  box-shadow: 0px 2px 4px -1px rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  border: 1px solid #ddd;
  border-bottom-color: #bbb;
`;


const PanelRowBlock = styled(PanelRow)`
  display:block;
  margin-bottom: 20px;
`;

const NoMatches = styled.div`
  display:block;
  padding: 20px;
  background-color: #eee;
  color: #888;
  text-align: center;
`;

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @param {Object}   props               Properties passed to the function.
 * @param {Object}   props.attributes    Available block attributes.
 * @param {Function} props.setAttributes Function that updates individual attributes.
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({attributes, setAttributes}) {
  const blockProps = useBlockProps();

  const colors = [
    {
      "name": "Black",
      "color": "#000000"
    },
    {
      "name": "White",
      "color": "#ffffff"
    },
    {
      "name": "Blue",
      "color": "#5376ea"
    }
  ]

  const renderTextColorPicker = () => (
    <ColorPalette
      value={attributes.button_text_color}
      colors={colors}
      onChange={(color) => setAttributes({button_text_color: color})}
    />
  );

  const renderBgColorPicker = () => (
    <ColorPalette
      value={attributes.button_bg_color}
      colors={colors}
      onChange={(color) => setAttributes({button_bg_color: color})}
    />
  );

  const generateStyles = () => {
    let styles = {}
    if (attributes.button_border_radius) styles.borderRadius = attributes.button_border_radius
    if (attributes.button_bg_color) styles.backgroundColor = attributes.button_bg_color
    if (attributes.button_text_color) styles.color = attributes.button_text_color
    if (attributes.button_font_size) styles.fontSize = attributes.button_font_size
    return styles
  }
  const isIncludedInIds = (id, ids) => {
    if(!ids) return true;
    let clean_item_ids = ids.split(",").map(item => item.trim());
    return clean_item_ids ? clean_item_ids.includes(id) : true;
  }

  const renderResources = () => {
    let resources = []
    switch (attributes.items) {
      case 'services':
        resources = latepoint_helper.services.filter((service) => isIncludedInIds(service.id, attributes.item_ids) && isIncludedInIds(service.category_id, attributes.group_ids))
        break;
      case 'agents':
        resources = latepoint_helper.agents.filter((agent) => isIncludedInIds(agent.id, attributes.item_ids))
        break;
      case 'locations':
        resources = latepoint_helper.locations.filter((location) => isIncludedInIds(location.id, attributes.item_ids) && isIncludedInIds(location.category_id, attributes.group_ids))
        break;
    }
    if(resources.length){
      if(attributes.limit) resources = resources.slice(0, attributes.limit)
      let resources_items = resources.map((resource) =>
        <Resource key={resource.id}>
          {resource.name}
          <LatepointBookButton style={generateStyles()}>{attributes.button_caption}</LatepointBookButton>
        </Resource>)
      return <ListOfResources className={`resources-columns-${attributes.columns}`}>{resources_items}</ListOfResources>
    }else{
      return <NoMatches>{__('No Items Matching', 'latepoint')}</NoMatches>
    }
  }


  return (
    <div {...blockProps}>
      <InspectorControls>
        <Panel>
          <PanelBody title="Settings">
            <PanelRowBlock>
              <SelectControl
                label={__('Resource Type', 'latepoint')}
                onChange={(value) => setAttributes({items: value})}
                value={attributes.items}
                options={[
                  {value: 'services', label: __('Services', 'latepoint')},
                  {value: 'agents', label: __('Agents', 'latepoint')},
                  {value: 'locations', label: __('Locations', 'latepoint')}]}
              />
            </PanelRowBlock>
            <PanelRowBlock>
              <SelectControl
                label={__('Number of columns', 'latepoint')}
                onChange={(value) => setAttributes({columns: value})}
                value={attributes.columns ?? '4'}
                options={[
                  {label: __('One', 'latepoint'), value: '1'},
                  {label: __('Two', 'latepoint'), value: '2'},
                  {label: __('Three', 'latepoint'), value: '3'},
                  {label: __('Four', 'latepoint'), value: '4'},
                  {label: __('Five', 'latepoint'), value: '5'}]}
              />
            </PanelRowBlock>
          </PanelBody>
        </Panel>
        <Panel>
          <PanelBody title="Booking Form Settings">
            <PanelRow>
              <ToggleControl
                label="Hide Summary Panel"
                checked={attributes.hide_summary}
                onChange={(value) => setAttributes({hide_summary: value})}
              />
            </PanelRow>
            <PanelRow>
              <ToggleControl
                label="Hide Side Panel"
                checked={attributes.hide_side_panel}
                onChange={(value) => setAttributes({hide_side_panel: value})}
              />
            </PanelRow>
          </PanelBody>
        </Panel>
        <Panel>
          <PanelBody title="Step Settings" initialOpen={false}>
            {attributes.items != 'agents' && <PanelRowBlock>
              <SelectControl
                value={attributes.selected_agent}
                label={__('Preselected Agent', 'latepoint')}
                onChange={(value) => setAttributes({selected_agent: value})}
                options={latepoint_helper.selected_agents_options}
              />
            </PanelRowBlock>}
            {attributes.items != 'services' && <PanelRowBlock>
              <SelectControl
                value={attributes.selected_service}
                label={__('Preselected Service', 'latepoint')}
                onChange={(value) => setAttributes({selected_service: value})}
                options={latepoint_helper.selected_services_options}
              />
            </PanelRowBlock>}
            {attributes.items != 'services' && <PanelRowBlock>
              <SelectControl
                value={attributes.selected_service_category}
                label={__('Preselected Service Category', 'latepoint')}
                onChange={(value) => setAttributes({selected_service_category: value})}
                options={latepoint_helper.selected_service_categories_options}
              />
            </PanelRowBlock>}
            {attributes.items != 'locations' && <PanelRowBlock>
              <SelectControl
                value={attributes.selected_location}
                label={__('Preselected Location', 'latepoint')}
                onChange={(value) => setAttributes({selected_location: value})}
                options={latepoint_helper.selected_locations_options}
              />
            </PanelRowBlock>}
            <PanelRow>
              <TextControl
                label={__('Preselected Booking Start Date', 'latepoint')}
                value={attributes.selected_start_date || ''}
                placeholder="YYYY-MM-DD"
                onChange={(value) => setAttributes({selected_start_date: value})}
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label={__('Preselected Booking Start Time', 'latepoint')}
                value={attributes.selected_start_time || ''}
                placeholder="Minutes"
                onChange={(value) => setAttributes({selected_start_time: value})}
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label={__('Preselected Duration', 'latepoint')}
                value={attributes.selected_duration || ''}
                placeholder="Minutes"
                onChange={(value) => setAttributes({selected_duration: value})}
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label={__('Preselected Total Attendees', 'latepoint')}
                value={attributes.selected_total_attendees || ''}
                placeholder="Number"
                onChange={(value) => setAttributes({selected_total_attendees: value})}
              />
            </PanelRow>
          </PanelBody>
        </Panel>
        <Panel>
          <PanelBody title="Items Settings" initialOpen={false}>
            <PanelRow>
              <TextControl
                label="Max Number of Items Shown"
                value={attributes.limit || ''}
                onChange={(value) => setAttributes({limit: value})}
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label="Show Selected Items"
                placeholder="Comma separated item IDs"
                value={attributes.item_ids || ''}
                onChange={(value) => setAttributes({item_ids: value})}
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label="Show Selected Categories"
                placeholder="Comma separated category IDs"
                value={attributes.group_ids || ''}
                onChange={(value) => setAttributes({group_ids: value})}
              />
            </PanelRow>
          </PanelBody>
        </Panel>
        <Panel>
          <PanelBody title="Other Settings" initialOpen={false}>
            <PanelRow>
              <TextControl
                label="Source ID"
                value={attributes.source_id || ''}
                onChange={(value) => setAttributes({source_id: value})}
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label="Calendar Start Date"
                value={attributes.calendar_start_date || ''}
                placeholder="YYYY-MM-DD"
                onChange={(value) => setAttributes({calendar_start_date: value})}
              />
            </PanelRow>
            {attributes.items != 'services' && <PanelRow>
              <TextControl
                label="Show Services"
                placeholder="Comma separated service IDs"
                value={attributes.show_services || ''}
                onChange={(value) => setAttributes({show_services: value})}
              />
            </PanelRow>}
            {attributes.items != 'services' &&
            <PanelRow>
              <TextControl
                label="Show Service Categories"
                placeholder="Comma separated category IDs"
                value={attributes.show_service_categories || ''}
                onChange={(value) => setAttributes({show_service_categories: value})}
              />
            </PanelRow>}
            {attributes.items != 'agents' && <PanelRow>
              <TextControl
                label="Show Agents"
                placeholder="Comma separated agent IDs"
                value={attributes.show_agents || ''}
                onChange={(value) => setAttributes({show_agents: value})}
              />
            </PanelRow>}
            {attributes.items != 'locations' && <PanelRow>
              <TextControl
                label="Show Locations"
                placeholder="Comma separated location IDs"
                value={attributes.show_locations || ''}
                onChange={(value) => setAttributes({show_locations: value})}
              />
            </PanelRow>}
          </PanelBody>
        </Panel>
        <Panel>
          <PanelBody title="Appearance" initialOpen={false}>
            <TextControl
              label="Button Caption"
              value={attributes.button_caption || ''}
              onChange={(value) => setAttributes({button_caption: value})}
            />
            <PanelRow>
              <ColorAttributesWrapper>

                <Dropdown
                  position="bottom left"
                  renderToggle={({isOpen, onToggle}) => (
                    <Button onClick={onToggle} aria-expanded={isOpen}>
                      <Flex>
                        <ColorIndicator colorValue={attributes.button_bg_color}/>
                        <FlexBlock>{__('Button Background')}</FlexBlock>
                      </Flex>
                    </Button>
                  )}
                  renderContent={renderBgColorPicker}
                />
                <Dropdown
                  position="bottom left"
                  renderToggle={({isOpen, onToggle}) => (
                    <Button onClick={onToggle} aria-expanded={isOpen}>
                      <Flex>
                        <ColorIndicator colorValue={attributes.button_text_color}/>
                        <FlexBlock>{__('Button Text Color')}</FlexBlock>
                      </Flex>
                    </Button>
                  )}
                  renderContent={renderTextColorPicker}
                />
              </ColorAttributesWrapper>
            </PanelRow>
            <PanelRowBlock>
              <FontSizePicker
                __nextHasNoMarginBottom
                fontSizes={[
                  {
                    name: __('Small'),
                    slug: 'small',
                    size: '12px',
                  },
                  {
                    name: __('Normal'),
                    slug: 'normal',
                    size: '18px',
                  },
                  {
                    name: __('Big'),
                    slug: 'big',
                    size: '28px',
                  },
                ]}
                value={attributes.button_font_size}
                fallbackFontSize={attributes.button_font_size}
                onChange={(value) => setAttributes({button_font_size: value})}
              />
            </PanelRowBlock>
            <PanelRowBlock>
              <UnitControl
                label={__('Button Border Radius')}
                onChange={(value) => {
                  setAttributes({button_border_radius: value})
                }}
                units={[
                  {
                    a11yLabel: 'Pixels (px)',
                    label: 'px',
                    step: 1,
                    value: 'px'
                  }
                ]}
                value={attributes.button_border_radius}
              />
            </PanelRowBlock>
          </PanelBody>
        </Panel>
      </InspectorControls>
      <ListOfResourcesWrapper>
        {renderResources()}
      </ListOfResourcesWrapper>
    </div>
  );
}
