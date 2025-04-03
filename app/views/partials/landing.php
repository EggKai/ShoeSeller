<div class="parallax">
  <section class="parallax__header">
    <div class="parallax__visuals">
      <div class="parallax__black-line-overflow"></div>
      <div data-parallax-layers class="parallax__layers">
        <img src="/public/assets/images/<?php echo $parralaxLayerBG??"landing-bg.jpg"; ?>" loading="eager" width="800" data-parallax-layer="2" alt="" class="parallax__layer-img">
        <div data-parallax-layer="3" class="parallax__layer-title">
          <h2 class="parallax__title"><?php echo $parralaxLayerText ?? "Shoe Seller";?></h2>
        </div>
        <img src="/public/assets/images/<?php echo $parralaxLayerFG??"landing-fg.png"; ?>" loading="eager" width="800" data-parallax-layer="4" alt="" class="parallax__layer-img fg">
      </div>
      <div class="parallax__fade"></div>
    </div>
  </section>
</div>
